<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A concert's own page: the concert, what is written about it, and the artist
 * it belongs to.
 */
final class ConcertInfo extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'concert' => [Concert::class, 'one'],
        'minPrice' => [ConcertMinPrice::class, 'one'],
        'covers' => [Cover::class, 'list'],
        'description' => [ConcertDescription::class, 'one'],
    ];

    public function __construct(
        public readonly ?Concert $concert = null,
        public readonly ?ConcertMinPrice $minPrice = null,
        /** @var list<Cover> */
        public readonly array $covers = [],
        public readonly ?ConcertDescription $description = null,
        public readonly ?int $leadArtistId = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->concert];
    }
}
