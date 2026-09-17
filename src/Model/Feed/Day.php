<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Feed;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * One day of the feed.
 */
final class Day extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'events' => [Event::class, 'list'],
        'tracksToPlayWithAds' => [TrackWithAds::class, 'list'],
        'tracksToPlay' => [Track::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $day = null,
        /** @var list<Event> */
        public readonly array $events = [],
        /** @var list<TrackWithAds> */
        public readonly array $tracksToPlayWithAds = [],
        /** @var list<Track> */
        public readonly array $tracksToPlay = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->day];
    }
}
