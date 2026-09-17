<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What an advertisement inserted into a station needs in order to be
 * requested and reported.
 */
final class AdParams extends Model
{
    public function __construct(
        public readonly string|int|null $partnerId = null,
        public readonly string|int|null $categoryId = null,
        public readonly ?string $pageRef = null,
        public readonly ?string $targetRef = null,
        public readonly ?string $otherParams = null,
        public readonly ?int $adVolume = null,
        public readonly ?string $genreId = null,
        public readonly ?string $genreName = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->partnerId, $this->categoryId, $this->pageRef];
    }
}
