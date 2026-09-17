<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Whether an artist has a trailer to play.
 */
final class ArtistTrailerStatus extends Model
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
