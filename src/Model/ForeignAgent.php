<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * The notice Russian law requires be shown for material by someone designated
 * a foreign agent.
 */
final class ForeignAgent extends Model
{
    public function __construct(
        public readonly ?string $reason = null,
        public readonly ?string $title = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->reason, $this->title];
    }
}
