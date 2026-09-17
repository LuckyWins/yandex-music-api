<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Search;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One kind of thing a search found, and where in the whole of it this page
 * sits.
 *
 * PHP has no generics, but the type is not lost: it is declared for static
 * analysis, so `$search->tracks->results[0]` is known to be a Track. Build
 * these with of() rather than fromApi() — the response alone does not say what
 * to build, so the model has to be named.
 *
 * @template T of Model
 */
final class SearchResult extends Model
{
    /**
     * @param list<T> $results
     */
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?int $total = null,
        public readonly ?int $perPage = null,
        public readonly ?int $order = null,
        public readonly array $results = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Build a result set of a given kind.
     *
     * The response carries its own `type`, and that wins when it disagrees
     * with what the caller expected: the service has been seen putting
     * playlists in the artists field, and following the field name there would
     * mean building an Artist out of a playlist — which, with models this
     * strict, throws and takes the whole search down with it. $type always
     * says what was actually built.
     *
     * @template TModel of Model
     *
     * @param class-string<TModel> $model the model this field is expected to hold
     *
     * @return self<TModel>|null
     */
    public static function of(string $model, string $expectedType, mixed $data, ?Client $client = null): ?self
    {
        if (!is_array($data) || [] === $data) {
            return null;
        }

        $declared = $data['type'] ?? null;
        $type = is_string($declared) ? $declared : $expectedType;

        /** @var class-string<TModel> $class */
        $class = (is_string($declared) ? SearchType::tryFrom($declared)?->model() : null) ?? $model;

        $total = $data['total'] ?? null;
        $perPage = $data['perPage'] ?? null;
        $order = $data['order'] ?? null;

        return new self(
            type: $type,
            total: is_int($total) ? $total : null,
            perPage: is_int($perPage) ? $perPage : null,
            order: is_int($order) ? $order : null,
            results: $class::listFromApi($data['results'] ?? null, $client),
            client: $client,
        );
    }

    protected function identity(): array
    {
        return [$this->type, $this->total, $this->order];
    }
}
