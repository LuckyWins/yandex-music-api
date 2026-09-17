<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A link an artist put on their page, with something to show for it.
 *
 * Not the same as Artist\Link, which is the older shape carried inside the
 * artist itself: this one has a subtitle and an image, and arrives from the
 * artist-links endpoint.
 */
final class ArtistLink extends Model
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $subtitle = null,
        public readonly ?string $url = null,
        public readonly ?string $imgUrl = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->url, $this->title];
    }
}
