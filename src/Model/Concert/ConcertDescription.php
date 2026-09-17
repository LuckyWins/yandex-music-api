<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What is written about a concert, and who wrote it.
 */
final class ConcertDescription extends Model
{
    public function __construct(
        public readonly ?string $text = null,
        public readonly ?string $source = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->text, $this->source];
    }
}
