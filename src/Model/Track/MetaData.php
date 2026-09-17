<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Tags carried by a track a user uploaded themselves.
 */
final class MetaData extends Model
{
    public function __construct(
        public readonly ?string $album = null,
        public readonly ?int $volume = null,
        public readonly ?int $year = null,
        public readonly ?int $number = null,
        public readonly ?string $genre = null,
        public readonly ?string $lyricist = null,
        public readonly ?string $version = null,
        public readonly ?string $composer = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->album, $this->volume, $this->year, $this->number, $this->genre];
    }
}
