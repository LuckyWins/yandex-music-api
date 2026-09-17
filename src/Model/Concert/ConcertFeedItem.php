<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One entry in the listing — a type beside the thing itself, the same
 * arrangement the landing blocks and an artist's clips use.
 */
final class ConcertFeedItem extends Model
{
    /**
     * The service says `concert_item`, the way it says `album_item` for a
     * pinned album. Both spellings are accepted here, because a listing that
     * quietly comes back empty is the worst way to find that out — which is
     * how this was found.
     *
     * @var array<string, class-string<Model>>
     */
    private const TYPES = [
        'concert_item' => ConcertFeedItemData::class,
        'concert' => ConcertFeedItemData::class,
    ];

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?ConcertFeedItemData $data = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * An entry of a kind this library does not know keeps its type and drops
     * its data rather than guessing.
     */
    protected static function prepare(array $args, array $data, ?Client $client): array
    {
        $type = $data['type'] ?? null;
        $model = is_string($type) ? (self::TYPES[$type] ?? null) : null;

        $args['data'] = null === $model ? null : $model::fromApi($data['data'] ?? null, $client);

        return $args;
    }

    protected function identity(): array
    {
        return [$this->type, $this->data];
    }
}
