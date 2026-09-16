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
 *         'artists'     => [Artist::class, 'list'],
 *         'major'       => [Major::class,  'one'],
 *  *     ];
 *
 * A model whose shape depends on the endpoint overrides fromApi() instead.
 *
 * Field names are matched case- and separator-insensitively, so a property
 * named `lastFmScrobblingEnabled` is filled by `lastFMScrobblingEnabled`,
 * `lastFmScrobblingEnabled` or `last_fm_scrobbling_enabled` alike. The API's
 * exact spelling is not always knowable in advance, and a near miss would
 * otherwise drop the field in silence.
 */
abstract class Model
{
    /**
     * Nested models to resolve after the raw fields are collected.
     *
     * @var array<string, array{0: class-string<self>, 1: 'one'|'list'}>
     */
    protected const NESTED = [];

    /** @var array<class-string, array{names: list<string>, required: list<string>, byKey: array<string, string>}> */
    private static array $fieldCache = [];

    /** @var array<string, string> */
    private static array $canonicalCache = [];

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

            $name = $meta['byKey'][self::canonical($key)] ?? null;

            if (null === $name) {
                $unknown[] = $key;
            } else {
                $args[$name] = $value;
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
     * Build a map of models, keyed as the API keyed them.
     *
     * For responses that use the object itself as a dictionary — arbitrary keys
     * pointing at uniform values — rather than a list.
     *
     * @return array<string, static>
     */
    public static function mapFromApi(mixed $data, ?Client $client = null): array
    {
        if (!is_array($data)) {
            return [];
        }

        $result = [];

        foreach ($data as $key => $item) {
            if (!is_string($key)) {
                continue;
            }

            $model = static::fromApi($item, $client);

            if (null !== $model) {
                $result[$key] = $model;
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
     * Constructor parameter names, which of them are required, and the lookup
     * from canonical key to parameter name. Memoized per class — this is what
     * stands in for Python's dataclass field introspection.
     *
     * @param class-string $class
     *
     * @return array{names: list<string>, required: list<string>, byKey: array<string, string>}
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
        $byKey = [];

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $names[] = $name;

            if (!$parameter->isOptional()) {
                $required[] = $name;
            }

            $key = self::canonical($name);

            if (isset($byKey[$key])) {
                // Two properties that no API key could tell apart. This is a
                // mistake in the model, and it is worth failing on the first
                // call rather than losing whichever field loses the race.
                throw new YandexMusicException(sprintf(
                    '%s declares both $%s and $%s, which are indistinguishable once case and '
                    .'separators are ignored. Rename one of them.',
                    $class,
                    $byKey[$key],
                    $name,
                ));
            }

            $byKey[$key] = $name;
        }

        return self::$fieldCache[$class] = ['names' => $names, 'required' => $required, 'byKey' => $byKey];
    }

    /**
     * Reduce a name to the form used for matching: no separators, no case.
     *
     * The API's exact spelling of a compound word cannot be predicted —
     * `lastFmScrobblingEnabled` and `lastFMScrobblingEnabled` are both
     * plausible, and the reference library's snake_case names cannot tell us
     * which one arrives. Matching on this form makes the question moot.
     */
    private static function canonical(string $key): string
    {
        return self::$canonicalCache[$key] ??= strtolower(str_replace(['-', '_'], '', $key));
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
