<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Genre;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A genre's name in one language.
 */
final class Title extends Model
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $fullTitle = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->title, $this->fullTitle];
    }
}
