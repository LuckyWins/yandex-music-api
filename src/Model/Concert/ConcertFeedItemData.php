<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A concert as the listing carries it, with its price alongside.
 */
final class ConcertFeedItemData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'concert' => [Concert::class, 'one'],
        'minPrice' => [ConcertMinPrice::class, 'one'],
    ];

    public function __construct(
        public readonly ?Concert $concert = null,
        public readonly ?ConcertMinPrice $minPrice = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->concert];
    }
}
