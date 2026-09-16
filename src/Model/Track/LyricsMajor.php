<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Who supplied a set of lyrics.
 */
final class LyricsMajor extends Model
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $prettyName,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id];
    }
}
