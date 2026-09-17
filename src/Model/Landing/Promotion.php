<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A promoted something on the front page, with everything needed to draw it.
 */
final class Promotion extends Model
{
    public function __construct(
        public readonly ?string $promoId = null,
        public readonly ?string $title = null,
        public readonly ?string $subtitle = null,
        public readonly ?string $heading = null,
        public readonly ?string $url = null,
        public readonly ?string $urlScheme = null,
        public readonly ?string $textColor = null,
        public readonly ?string $gradient = null,
        public readonly ?string $image = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->promoId];
    }
}
