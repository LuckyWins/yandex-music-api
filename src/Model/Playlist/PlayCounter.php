<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * How many days in a row the owner has listened to a daily playlist, and the
 * phrase the app shows for it.
 */
final class PlayCounter extends Model
{
    public function __construct(
        public readonly int $value,
        public readonly string $description,
        public readonly bool $updated,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->value, $this->description];
    }
}
