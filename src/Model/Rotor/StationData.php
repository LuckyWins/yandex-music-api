<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The personal radio station attached to an account.
 */
final class StationData extends Model
{
    public function __construct(
        public readonly string $name,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->name];
    }
}
