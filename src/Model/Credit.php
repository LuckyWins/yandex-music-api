<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * One line of a credit list: who did what.
 */
final class Credit extends Model
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $value = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->title, $this->value];
    }
}
