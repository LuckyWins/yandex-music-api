<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The envelope /playlists answers with.
 */
final class PlaylistsList extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'playlists' => [Playlist::class, 'list'],
    ];

    public function __construct(
        /** @var list<Playlist> */
        public readonly array $playlists = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->playlists];
    }
}
