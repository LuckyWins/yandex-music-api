<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * Tracks the service suggests adding to a playlist.
 */
final class PlaylistRecommendations extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'tracks' => [Track::class, 'list'],
    ];

    public function __construct(
        /** @var list<Track> */
        public readonly array $tracks = [],
        public readonly ?string $batchId = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->batchId, $this->tracks];
    }
}
