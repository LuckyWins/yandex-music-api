<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * Whether a trailer exists for something.
 */
final class Trailer extends Model
{
    public function __construct(
        public readonly ?bool $available = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->available];
    }
}
