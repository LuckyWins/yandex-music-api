<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A concert: when, where, how much, and where to buy a ticket.
 */
final class Concert extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'minPrice' => [ConcertMinPrice::class, 'one'],
        'cashback' => [ConcertCashback::class, 'one'],
        'eventInfo' => [ConcertEventInfo::class, 'one'],
        'cover' => [Cover::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $concertTitle = null,
        public readonly ?string $city = null,
        public readonly ?string $place = null,
        public readonly ?string $address = null,
        /** When it happens, as the service formats it. */
        public readonly ?string $datetime = null,
        public readonly ?string $afishaUrl = null,
        public readonly ?string $contentRating = null,
        public readonly ?string $imageUrl = null,
        /** @var list<string> */
        public readonly array $images = [],
        public readonly ?Cover $cover = null,
        public readonly ?ConcertMinPrice $minPrice = null,
        public readonly ?ConcertCashback $cashback = null,
        public readonly ?ConcertEventInfo $eventInfo = null,
        public readonly ?string $dataSessionId = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id];
    }
}
