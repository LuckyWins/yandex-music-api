<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Where a track fades in and out, in seconds from its start.
 */
final class Fade extends Model
{
    public function __construct(
        public readonly ?float $inStart = null,
        public readonly ?float $inStop = null,
        public readonly ?float $outStart = null,
        public readonly ?float $outStop = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->inStart, $this->inStop, $this->outStart, $this->outStop];
    }
}
