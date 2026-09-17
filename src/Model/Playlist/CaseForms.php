<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A name in all six Russian cases.
 *
 * Sent so that a phrase built around a user's name can be grammatical:
 * "Плейлист дня Андрея" needs the genitive, not the nominative.
 */
final class CaseForms extends Model
{
    public function __construct(
        public readonly string $nominative,
        public readonly string $genitive,
        public readonly string $dative,
        public readonly string $accusative,
        public readonly string $instrumental,
        public readonly string $prepositional,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->nominative];
    }
}
