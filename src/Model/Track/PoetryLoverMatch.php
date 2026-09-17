<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Where a searched-for phrase sits inside the lyrics.
 */
final class PoetryLoverMatch extends Model
{
    public function __construct(
        public readonly int $begin,
        public readonly int $end,
        public readonly int $line,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->begin, $this->end, $this->line];
    }
}
