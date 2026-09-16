<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * The personal radio station offered for an artist or album, and how to
 * present it.
 */
final class CustomWave extends Model
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $animationUrl = null,
        public readonly ?string $header = null,
        public readonly ?string $backgroundImageUrl = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->title, $this->header];
    }
}
