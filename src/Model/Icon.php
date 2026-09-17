<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * A station's artwork: a picture and the color to show behind it.
 */
final class Icon extends Model
{
    public function __construct(
        public readonly ?string $backgroundColor = null,
        public readonly ?string $imageUrl = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The image at a given size. The API stores `%%` where the size belongs.
     */
    public function url(string $size = '200x200'): ?string
    {
        return null === $this->imageUrl ? null : 'https://'.str_replace('%%', $size, $this->imageUrl);
    }

    protected function identity(): array
    {
        return [$this->imageUrl, $this->backgroundColor];
    }
}
