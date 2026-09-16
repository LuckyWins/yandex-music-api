<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Album;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A call to action shown on an album, such as a pre-save prompt.
 */
final class AlbumActionButton extends Model
{
    public function __construct(
        public readonly ?string $text = null,
        public readonly ?string $url = null,
        public readonly ?string $color = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->text, $this->url, $this->color];
    }
}
