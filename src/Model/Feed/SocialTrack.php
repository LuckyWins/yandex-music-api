<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Feed;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * A track the feed is showing because people you follow liked it.
 *
 * The reference library does not model this; it was found in a live feed.
 */
final class SocialTrack extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'track' => [Track::class, 'one'],
        'likedByUsers' => [User::class, 'list'],
    ];

    public function __construct(
        public readonly ?Track $track = null,
        /** @var list<User> */
        public readonly array $likedByUsers = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->track];
    }
}
