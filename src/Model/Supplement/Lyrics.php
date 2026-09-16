<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Supplement;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Lyrics as returned alongside a track's supplement.
 *
 * @deprecated Use Client::tracksLyrics(), which is the supported route and
 *             also offers the synced form.
 */
final class Lyrics extends Model
{
    public function __construct(
        public readonly int $id,
        /** The opening lines only. */
        public readonly string $lyrics,
        public readonly string $fullLyrics,
        public readonly bool $hasRights,
        public readonly bool $showTranslation,
        public readonly ?string $textLanguage = null,
        /** Where a translation came from, usually genius.com. */
        public readonly ?string $url = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->lyrics, $this->fullLyrics, $this->hasRights, $this->textLanguage, $this->showTranslation];
    }
}
