<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Album;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Where an album has been superseded by another one.
 */
final class Deprecation extends Model
{
    public function __construct(
        public readonly ?int $targetAlbumId = null,
        public readonly ?string $status = null,
        public readonly ?bool $done = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->targetAlbumId, $this->status, $this->done];
    }
}
