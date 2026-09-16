<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The button on an alert banner.
 */
final class AlertButton extends Model
{
    public function __construct(
        public readonly string $text,
        public readonly string $bgColor,
        public readonly string $textColor,
        public readonly string $uri,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->text, $this->uri];
    }
}
