<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Which kinds of lyrics exist for a track.
 *
 * Check this before asking for the synced form; requesting what is not there
 * comes back as a not-found rather than an empty result.
 */
final class LyricsInfo extends Model
{
    public function __construct(
        public readonly bool $hasAvailableSyncLyrics,
        public readonly bool $hasAvailableTextLyrics,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->hasAvailableSyncLyrics, $this->hasAvailableTextLyrics];
    }
}
