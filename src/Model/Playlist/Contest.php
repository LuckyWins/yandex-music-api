<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A playlist's entry in a Yandex.Music contest.
 */
final class Contest extends Model
{
    public function __construct(
        public readonly string $contestId,
        public readonly string $status,
        public readonly bool $canEdit,
        public readonly ?string $sent = null,
        public readonly ?string $withdrawn = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->contestId, $this->status];
    }
}
