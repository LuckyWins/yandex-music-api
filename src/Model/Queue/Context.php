<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Queue;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Where a queue came from: a playlist, an album, a station.
 */
final class Context extends Model
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $id = null,
        public readonly ?string $description = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->type, $this->id];
    }
}
