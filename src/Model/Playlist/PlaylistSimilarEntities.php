<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Wave\SimilarEntityItem;

/**
 * Things to listen to next when this playlist runs out.
 */
final class PlaylistSimilarEntities extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'items' => [SimilarEntityItem::class, 'list'],
    ];

    public function __construct(
        /** @var list<SimilarEntityItem> */
        public readonly array $items = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->items];
    }
}
