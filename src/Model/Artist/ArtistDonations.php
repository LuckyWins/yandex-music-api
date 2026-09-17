<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The ways an artist can be supported.
 */
final class ArtistDonations extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'donations' => [ArtistDonationItem::class, 'list'],
    ];

    public function __construct(
        /** @var list<ArtistDonationItem> */
        public readonly array $donations = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->donations];
    }
}
