<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A reference to a playlist rather than the playlist itself: its owner and its
 * kind, which is what identifies one.
 */
final class PlaylistId extends Model
{
    public function __construct(
        public readonly ?int $uid = null,
        public readonly ?int $kind = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The pair as the batch endpoints want it, `{uid}:{kind}`.
     */
    public function pair(): ?string
    {
        if (null === $this->uid || null === $this->kind) {
            return null;
        }

        return $this->uid.':'.$this->kind;
    }

    protected function identity(): array
    {
        return [$this->uid, $this->kind];
    }
}
