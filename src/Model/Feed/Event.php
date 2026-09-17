<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Feed;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * One thing the feed has to say on a given day.
 *
 * Which of the three lists is filled depends on $type — an event about an
 * artist carries artists, one about a new album carries albums.
 */
final class Event extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'tracks' => [Track::class, 'list'],
        'artists' => [ArtistEvent::class, 'list'],
        'albums' => [AlbumEvent::class, 'list'],
        'socialTracks' => [SocialTrack::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
        public readonly ?string $typeForFrom = null,
        public readonly ?string $title = null,
        /** @var list<Track> */
        public readonly array $tracks = [],
        /** @var list<ArtistEvent> */
        public readonly array $artists = [],
        /** @var list<AlbumEvent> */
        public readonly array $albums = [],
        public readonly ?string $message = null,
        public readonly ?string $device = null,
        public readonly ?int $tracksCount = null,
        public readonly ?string $genre = null,
        /** @var list<SocialTrack> tracks surfaced because people you follow liked them */
        public readonly array $socialTracks = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->type];
    }
}
