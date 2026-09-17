<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Feed;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * A track in a day's playback queue, or the slot where an advertisement goes.
 */
final class TrackWithAds extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'track' => [Track::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?Track $track = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->type, $this->track];
    }
}
