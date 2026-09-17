<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * How the concerts tab is laid out: how much goes in the top section and how
 * much in the feed below it.
 */
final class ConcertTabConfigData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'top' => [ConcertTabRange::class, 'one'],
        'feed' => [ConcertTabRange::class, 'one'],
    ];

    public function __construct(
        public readonly ?ConcertTabRange $top = null,
        public readonly ?ConcertTabRange $feed = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->top, $this->feed];
    }
}
