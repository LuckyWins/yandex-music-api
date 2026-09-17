<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What kind of event this is — a concert, a festival.
 */
final class ConcertEventInfo extends Model
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->type];
    }
}
