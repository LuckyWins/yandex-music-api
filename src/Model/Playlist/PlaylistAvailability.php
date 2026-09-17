<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Whether a playlist's trailer can be played.
 *
 * The same shape as the album and artist trailer flag, kept separate because
 * a playlist's arrives under a different key and may yet grow fields.
 */
final class PlaylistAvailability extends Model
{
    public function __construct(
        public readonly ?bool $available = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->available];
    }
}
