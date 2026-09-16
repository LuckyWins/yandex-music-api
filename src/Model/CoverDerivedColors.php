<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * Colours pulled out of cover art, so an interface can tint itself to match.
 */
final class CoverDerivedColors extends Model
{
    public function __construct(
        public readonly ?string $average = null,
        public readonly ?string $waveText = null,
        public readonly ?string $miniPlayer = null,
        public readonly ?string $accent = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->average, $this->waveText, $this->miniPlayer, $this->accent];
    }
}
