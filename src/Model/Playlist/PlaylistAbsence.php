<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Why a playlist that was asked for is not there.
 */
final class PlaylistAbsence extends Model
{
    public function __construct(
        public readonly int $kind,
        public readonly string $reason,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->kind, $this->reason];
    }
}
