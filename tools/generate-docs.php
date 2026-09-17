<?php

declare(strict_types=1);

/**
 * Generate the model and endpoint reference from the code itself.
 *
 * Hand-written documentation of typed signatures rots: the code changes, the
 * prose does not, and nobody notices until someone trusts the prose. Everything
 * in docs/models.md and docs/endpoints.md is therefore derived by reflection,
 * and CI fails if the committed files no longer match what this produces.
 *
 * What cannot be derived — why a decision was made, how we differ from the
 * reference library — is written by hand under docs/porting/ instead.
 *
 * Run it with `make docs`.
 */

namespace LuckyWins\YandexMusic\Tools;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;

require __DIR__.'/../vendor/autoload.php';

const SRC = __DIR__.'/../src';
const DOCS = __DIR__.'/../docs';

/**
 * Every class under src/, as a fully qualified name.
 *
 * @return list<class-string>
 */
function discoverClasses(): array
{
    $found = [];
    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(SRC));

    foreach ($files as $file) {
        if (!$file instanceof \SplFileInfo || 'php' !== $file->getExtension()) {
            continue;
        }

        $relative = substr($file->getRealPath() ?: '', strlen(realpath(SRC) ?: '') + 1);
        $class = 'LuckyWins\\YandexMusic\\'.str_replace(['/', '.php'], ['\\', ''], $relative);

        if (class_exists($class) || trait_exists($class)) {
            $found[] = $class;
        }
    }

    sort($found);

    return $found;
}

/**
 * The first sentence of a docblock, as prose.
 */
function summary(string|false $docblock): string
{
    if (false === $docblock) {
        return '';
    }

    $text = preg_replace('#^\s*/?\*+/?#m', '', $docblock) ?? '';
    $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');

    // Stop at the first annotation; those are for tooling, not readers.
    $text = preg_replace('/\s*@\w+.*$/', '', $text) ?? $text;

    if (1 === preg_match('/^(.+?\.)(\s|$)/', $text, $m)) {
        return trim($m[1]);
    }

    return trim($text);
}

/**
 * The type to show for a property: the docblock's if it says more than the
 * signature can, otherwise the declared one.
 */
function fieldType(ReflectionProperty $property, ReflectionParameter $parameter): string
{
    $doc = $property->getDocComment();

    if (false !== $doc && 1 === preg_match('/@var\s+([^\s*]+)/', $doc, $m)) {
        return shorten($m[1]);
    }

    $type = $parameter->getType();

    return $type instanceof ReflectionNamedType ? shorten(typeName($type)) : 'mixed';
}

/**
 * A type as it should read: nullable marked, except for mixed, which already
 * admits null.
 */
function typeName(ReflectionNamedType $type): string
{
    if ('mixed' === $type->getName()) {
        return 'mixed';
    }

    return ($type->allowsNull() ? '?' : '').$type->getName();
}

/**
 * Drop namespaces, keeping what a reader needs.
 */
function shorten(string $type): string
{
    return preg_replace('/[\\\\\w]+\\\\(\w+)/', '$1', $type) ?? $type;
}

/**
 * @param class-string<Model> $class
 */
function documentModel(string $class): string
{
    $reflection = new ReflectionClass($class);
    $constructor = $reflection->getConstructor();

    $out = '### '.$reflection->getShortName()."\n\n";

    $summary = summary($reflection->getDocComment());
    if ('' !== $summary) {
        $out .= $summary."\n\n";
    }

    if (null === $constructor) {
        return $out;
    }

    /** @var array<string, array{0: string, 1: string}> $nested */
    $nested = [];
    $constant = $reflection->getReflectionConstant('NESTED');
    if (false !== $constant) {
        /** @var array<string, array{0: class-string, 1: string}> $declared */
        $declared = $constant->getValue();
        foreach ($declared as $field => [$target, $kind]) {
            $nested[$field] = [shorten($target), $kind];
        }
    }

    $rows = [];

    foreach ($constructor->getParameters() as $parameter) {
        if ('client' === $parameter->getName()) {
            continue;
        }

        $property = $reflection->getProperty($parameter->getName());
        $note = summary($property->getDocComment());

        if (isset($nested[$parameter->getName()])) {
            [$target, $kind] = $nested[$parameter->getName()];
            $relation = 'list' === $kind ? "list of [$target](#".strtolower($target).')' : "[$target](#".strtolower($target).')';
            $note = '' === $note ? $relation : $relation.'. '.$note;
        }

        $rows[] = sprintf(
            '| `%s` | `%s` | %s | %s |',
            $parameter->getName(),
            fieldType($property, $parameter),
            $parameter->isOptional() ? 'no' : '**yes**',
            str_replace('|', '\\|', $note),
        );
    }

    if ([] === $rows) {
        return $out;
    }

    $out .= "| Field | Type | Required | Notes |\n|---|---|---|---|\n".implode("\n", $rows)."\n\n";

    return $out;
}

/**
 * The HTTP verb and path a client method uses, read out of its body.
 *
 * Paths are built by concatenation, so anything that is not a string literal —
 * an id, a kind, a genre — shows as a placeholder. Reading the source beats
 * repeating the URL in a docblock that nobody updates.
 *
 * @return list<string> one entry per request the method makes
 */
function endpointsOf(ReflectionMethod $method): array
{
    $file = $method->getFileName();
    $start = $method->getStartLine();
    $end = $method->getEndLine();

    if (false === $file || false === $start || false === $end) {
        return [];
    }

    $lines = file($file);
    if (false === $lines) {
        return [];
    }

    $body = implode('', array_slice($lines, $start - 1, $end - $start + 1));
    $found = [];

    $calls = [
        '/\$this->request->(get|post|put|delete)\(/' => null,
        '/\$this->(getArray|postArray)\(/' => null,
    ];

    foreach (array_keys($calls) as $pattern) {
        if (0 === preg_match_all($pattern, $body, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            continue;
        }

        foreach ($matches as $match) {
            $verb = strtoupper(str_replace('Array', '', $match[1][0]));
            $offset = (int) $match[0][1] + strlen($match[0][0]);
            $path = readPath(substr($body, $offset));

            if ('' !== $path) {
                $found[] = $verb.' '.$path;
            }
        }
    }

    sort($found);

    return array_values(array_unique($found));
}

/**
 * Turn the first argument of a request call into a readable path.
 *
 * Reads to the argument's end, keeps the string literals, and replaces
 * everything else with a placeholder naming what it was.
 */
function readPath(string $tail): string
{
    $depth = 0;
    $expression = '';

    for ($i = 0, $length = strlen($tail); $i < $length; ++$i) {
        $char = $tail[$i];

        if ('(' === $char || '[' === $char) {
            ++$depth;
        } elseif (')' === $char || ']' === $char) {
            if (0 === $depth) {
                break;
            }
            --$depth;
        } elseif (',' === $char && 0 === $depth) {
            break;
        }

        $expression .= $char;
    }

    $parts = [];

    foreach (explode('.', $expression) as $piece) {
        $piece = trim($piece);

        if ('' === $piece) {
            continue;
        }

        if (1 === preg_match("/^'(.*)'$/s", $piece, $m)) {
            $parts[] = $m[1];

            continue;
        }

        if (str_contains($piece, 'getBaseUrl') || str_contains($piece, 'BASE_URL')) {
            continue;
        }

        // An id, a kind, a genre — whatever the caller passes in.
        $name = 1 === preg_match('/(\w+)\(\)$|\$(\w+)$/', $piece, $m)
            ? ($m[2] ?? $m[1])
            : 'value';

        $parts[] = '{'.$name.'}';
    }

    return implode('', $parts);
}

/** @return array<string, string> */
function documentEndpoints(): array
{
    $sections = [];

    foreach (discoverClasses() as $class) {
        if (!str_starts_with($class, 'LuckyWins\\YandexMusic\\Client\\') || !trait_exists($class)) {
            continue;
        }

        $reflection = new ReflectionClass($class);
        $rows = [];

        foreach ((new ReflectionClass(Client::class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (Client::class !== $method->getDeclaringClass()->getName()) {
                continue;
            }

            if (false === $reflection->hasMethod($method->getName())) {
                continue;
            }

            $own = $reflection->getMethod($method->getName());
            $endpoints = endpointsOf($own);
            $returns = $method->getReturnType();

            $rows[] = sprintf(
                '| `%s()` | %s | `%s` | %s |',
                $method->getName(),
                [] === $endpoints ? '—' : '`'.implode('`, `', $endpoints).'`',
                $returns instanceof ReflectionNamedType ? shorten(typeName($returns)) : 'mixed',
                summary($own->getDocComment()),
            );
        }

        if ([] !== $rows) {
            sort($rows);
            $sections[$reflection->getShortName()] = "| Method | Request | Returns | Notes |\n|---|---|---|---|\n".implode("\n", $rows)."\n";
        }
    }

    return $sections;
}

// --- models.md ---------------------------------------------------------------

/** @var array<string, list<class-string<Model>>> $byNamespace */
$byNamespace = [];

foreach (discoverClasses() as $class) {
    if (!class_exists($class) || !is_subclass_of($class, Model::class)) {
        continue;
    }

    $namespace = (new ReflectionClass($class))->getNamespaceName();
    $group = str_replace('LuckyWins\\YandexMusic\\Model', '', $namespace);
    $byNamespace[trim($group, '\\') ?: 'Top level'][] = $class;
}

ksort($byNamespace);

$models = "# Models\n\n"
    ."Generated from the source by `make docs` — do not edit. Decisions and\n"
    ."divergences from the Python reference are written by hand under\n"
    ."[porting/](porting/).\n\n"
    ."A required field missing from a response raises rather than defaulting: it\n"
    ."means the API changed shape. List fields default to empty, object fields to\n"
    ."null.\n\n";

foreach ($byNamespace as $group => $classes) {
    $models .= "## $group\n\n";
    foreach ($classes as $class) {
        $models .= documentModel($class);
    }
}

// --- endpoints.md ------------------------------------------------------------

$endpoints = "# Endpoints\n\n"
    ."Generated from the source by `make docs` — do not edit.\n\n"
    ."One trait per domain, and every method returns typed models.\n\n"
    ."A dash in the request column means the method issues no request of its own —\n"
    ."it delegates to another one. Braces mark the parts of a path the caller\n"
    ."supplies.\n\n";

foreach (documentEndpoints() as $trait => $table) {
    $endpoints .= "## $trait\n\n".$table."\n";
}

if (!is_dir(DOCS)) {
    mkdir(DOCS, 0o755, true);
}

file_put_contents(DOCS.'/models.md', $models);
file_put_contents(DOCS.'/endpoints.md', $endpoints);

printf("docs/models.md     %d models\n", array_sum(array_map('count', $byNamespace)));
printf("docs/endpoints.md  %d traits\n", count(documentEndpoints()));
