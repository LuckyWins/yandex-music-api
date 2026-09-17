<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A link to a mix — one of the coloured tiles on the front page.
 */
final class MixLink extends Model
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $url = null,
        public readonly ?string $urlScheme = null,
        public readonly ?string $textColor = null,
        public readonly ?string $backgroundColor = null,
        public readonly ?string $backgroundImageUri = null,
        public readonly ?string $coverWhite = null,
        public readonly ?string $coverUri = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->title, $this->url];
    }
}
