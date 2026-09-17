<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A sponsor's dressing for a playlist: artwork, colors and the tracking
 * pixels that come with paid placement.
 */
final class Brand extends Model
{
    public function __construct(
        public readonly string $image,
        public readonly string $background,
        public readonly string $reference,
        /** @var list<string> */
        public readonly array $pixels,
        public readonly string $theme,
        public readonly string $playlistTheme,
        public readonly string $button,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->reference, $this->image];
    }
}
