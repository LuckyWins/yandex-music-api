<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * The tracks a playlist's trailer is built from.
 */
final class TrailerInfo extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'tracks' => [Track::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $title = null,
        /** @var list<Track> */
        public readonly array $tracks = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->title, $this->tracks];
    }
}
