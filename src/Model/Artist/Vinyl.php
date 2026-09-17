<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A record for sale, as brief-info offers it.
 */
final class Vinyl extends Model
{
    public function __construct(
        public readonly ?string $url = null,
        public readonly ?string $title = null,
        public readonly ?int $year = null,
        public readonly ?int $price = null,
        public readonly ?string $media = null,
        public readonly ?int $offerId = null,
        /** @var list<int> */
        public readonly array $artistIds = [],
        public readonly ?string $picture = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->offerId, $this->url];
    }
}
