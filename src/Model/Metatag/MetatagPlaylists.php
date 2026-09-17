<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Metatag;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;

/**
 * A page of the playlists under a tag.
 */
final class MetatagPlaylists extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'title' => [MetatagTitle::class, 'one'],
        'pager' => [Pager::class, 'one'],
        'playlists' => [Playlist::class, 'list'],
        'sortByValues' => [MetatagSortByValue::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?MetatagTitle $title = null,
        public readonly ?string $coverUri = null,
        public readonly ?string $color = null,
        public readonly ?string $stationId = null,
        public readonly ?Pager $pager = null,
        /** @var list<Playlist> */
        public readonly array $playlists = [],
        /** @var list<MetatagSortByValue> */
        public readonly array $sortByValues = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->pager];
    }
}
