<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Where to send an artist money, and what for.
 */
final class ArtistDonationData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artist' => [Artist::class, 'one'],
        'goal' => [ArtistDonationGoal::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $tipUrl = null,
        public readonly ?Artist $artist = null,
        public readonly ?ArtistDonationGoal $goal = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->tipUrl, $this->artist];
    }
}
