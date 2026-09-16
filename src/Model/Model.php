<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use ReflectionClass;

/**
 * Base class for every model.
 *
 * Deserialization works the same way for all of them: normalize the keys of the
 * decoded response, keep those that name a constructor parameter, hand the rest
 * to the unknown-field reporter, resolve whatever NESTED declares, and splat the
 * survivors in as named arguments. Filtering before construction is not
 * optional — an unknown named argument is a fatal error in PHP.
 *
 * The set of known fields comes from reflection over the promoted constructor
 * parameters, memoized per class. Subclasses therefore declare their shape once,
 * in the constructor, and get deserialization for free.
 *
 * Nested models are declared rather than hand-coded:
 *
 *     protected const NESTED = [
 *         'artists' => [Artist::class, 'list'],
 *         'major'   => [Major::class,  'one'],
 *     ];
 *
 * A model whose shape depends on the endpoint overrides fromApi() instead.
 */
abstract class Model
{
    /**
     * Nested models to resolve after the raw fields are collected.
     *
     * @var array<string, array{0: class-string<self>, 1: 'one'|'list'}>
     */
    protected const NESTED = [];

    /** @var array<class-string, array{names: list<string>, required: list<string>}> */
    private static array $fieldCache = [];

    /** @var array<string, string> */
    private static array $keyCache = [];

    /**
     * Build a model from a decoded API response.
     *
     * An empty payload yields null rather than an empty model, matching the
     * reference library — callers and tests both rely on it.
     */
    public static function fromApi(mixed $data, ?Client $client = null): ?static
    {
        if (!is_array($data) || [] === $data) {
            return null;
        }

        $meta = self::metaOf(static::class);
        $known = $meta['names'];
        $args = [];
        $unknown = [];

        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            $name = self::normalizeKey($key);

            if (in_array($name, $known, true)) {
                $args[$name] = $value;
            } else {
                $unknown[] = $name;
            }
        }

        foreach (static::NESTED as $name => [$class, $kind]) {
            if (!in_array($name, $known, true)) {
                continue;
            }

            $raw = $args[$name] ?? null;

            $args[$name] = 'list' === $kind
                ? $class::listFromApi($raw, $client)
                : $class::fromApi($raw, $client);
        }

        if ([] !== $unknown && true === $client?->reportsUnknownFields()) {
            $client->reportUnknownFields(static::class, $unknown);
        }

        if (in_array('client', $known, true)) {
            $args['client'] = $client;
        }

        $missing = array_values(array_diff($meta['required'], array_keys($args)));

        if ([] !== $missing) {
            // The API sent a shape this model does not expect, which in practice
            // means Yandex changed the response. Naming the fields makes that
            // diagnosable from the message alone.
            throw new YandexMusicException(sprintf(
                'Response is missing required fields for %s: %s',
                static::class,
                implode(', ', $missing),
            ));
        }

        /** @var ReflectionClass<static> $reflection */
        $reflection = new ReflectionClass(static::class);

        return $reflection->newInstanceArgs($args);
    }

    /**
     * Build a list of models. Always an array, never null — list-typed fields
     * are empty arrays when the API sends nothing, not nulls.
     *
     * @return list<static>
     */
    public static function listFromApi(mixed $data, ?Client $client = null): array
    {
        if (!is_array($data)) {
            return [];
        }

        $result = [];

        foreach ($data as $item) {
            $model = static::fromApi($item, $client);

            if (null !== $model) {
                $result[] = $model;
            }
        }

        return $result;
    }

    /**
     * Compare by the fields that define identity rather than by every field.
     *
     * Two objects describing the same track are the same track even when one of
     * them was fetched with fewer fields populated.
     */
    public function equals(mixed $other): bool
    {
        if (!$other instanceof self || $other::class !== static::class) {
            return false;
        }

        $mine = $this->identity();

        if ([] === $mine) {
            return $this === $other;
        }

        $theirs = $other->identity();

        if (count($mine) !== count($theirs)) {
            return false;
        }

        foreach ($mine as $index => $value) {
            if (!self::valuesEqual($value, $theirs[$index] ?? null)) {
                return false;
            }
        }

        return true;
    }

    /**
     * The model as a plain array, with the client back-reference dropped.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $result = [];

        foreach (get_object_vars($this) as $name => $value) {
            if ('client' === $name) {
                continue;
            }

            $result[$name] = self::unwrap($value);
        }

        return $result;
    }

    /**
     * The fields that define this model's identity. Empty means identity is
     * object identity, which is the default for models with nothing to key on.
     *
     * @return list<mixed>
     */
    protected function identity(): array
    {
        return [];
    }

    /**
     * Constructor parameter names and which of them are required, memoized per
     * class. This is what stands in for Python's dataclass field introspection.
     *
     * @param class-string $class
     *
     * @return array{names: list<string>, required: list<string>}
     */
    private static function metaOf(string $class): array
    {
        if (isset(self::$fieldCache[$class])) {
            return self::$fieldCache[$class];
        }

        $constructor = (new ReflectionClass($class))->getConstructor();
        $parameters = null === $constructor ? [] : $constructor->getParameters();

        $names = [];
        $required = [];

        foreach ($parameters as $parameter) {
            $names[] = $parameter->getName();

            if (!$parameter->isOptional()) {
                $required[] = $parameter->getName();
            }
        }

        return self::$fieldCache[$class] = ['names' => $names, 'required' => $required];
    }

    /**
     * Convert an API field name to a PHP property name.
     *
     * The music API sends camelCase, which passes through untouched. The OAuth
     * endpoints send snake_case (`access_token`) and a few API fields are
     * hyphenated (`req-id`), so both separators collapse into camelCase.
     */
    private static function normalizeKey(string $key): string
    {
        if (isset(self::$keyCache[$key])) {
            return self::$keyCache[$key];
        }

        $normalized = $key;

        if (str_contains($key, '_') || str_contains($key, '-')) {
            $parts = array_values(array_filter(
                preg_split('/[-_]+/', $key) ?: [],
                static fn (string $part): bool => '' !== $part,
            ));

            if ([] !== $parts) {
                $first = array_shift($parts);
                $normalized = $first.implode('', array_map(ucfirst(...), $parts));
            }
        }

        return self::$keyCache[$key] = $normalized;
    }

    private static function valuesEqual(mixed $a, mixed $b): bool
    {
        if ($a instanceof self) {
            return $a->equals($b);
        }

        if (is_array($a) && is_array($b)) {
            if (count($a) !== count($b)) {
                return false;
            }

            foreach ($a as $key => $value) {
                if (!array_key_exists($key, $b) || !self::valuesEqual($value, $b[$key])) {
                    return false;
                }
            }

            return true;
        }

        return $a === $b;
    }

    private static function unwrap(mixed $value): mixed
    {
        if ($value instanceof self) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return array_map(self::unwrap(...), $value);
        }

        return $value;
    }
}
