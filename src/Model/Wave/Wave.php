<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Wave;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A personal radio station — what it plays and what it was seeded from.
 */
final class Wave extends Model
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        /** @var list<string> */
        public readonly array $seeds = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->name, $this->seeds];
    }
}
