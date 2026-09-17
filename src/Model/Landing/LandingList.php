<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistId;

/**
 * A page of one kind of thing — new releases, new playlists or podcasts.
 *
 * Only the list matching what was asked for is filled: releases and podcasts
 * arrive as bare album ids, playlists as owner-and-kind pairs. Fetch the
 * objects themselves with albums() or playlists().
 */
final class LandingList extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'newPlaylists' => [PlaylistId::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $typeForFrom = null,
        public readonly ?string $title = null,
        public readonly ?string $id = null,
        /** @var list<int> album ids */
        public readonly array $newReleases = [],
        /** @var list<PlaylistId> */
        public readonly array $newPlaylists = [],
        /** @var list<int> album ids */
        public readonly array $podcasts = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->type];
    }
}
