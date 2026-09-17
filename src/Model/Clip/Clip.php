<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Clip;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\ContentRestrictions;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A short video for a track.
 */
final class Clip extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artists' => [Artist::class, 'list'],
        'cover' => [Cover::class, 'one'],
        'contentRestrictions' => [ContentRestrictions::class, 'one'],
    ];

    public function __construct(
        public readonly ?int $clipId = null,
        public readonly ?string $title = null,
        public readonly ?string $version = null,
        public readonly ?string $playerId = null,
        public readonly ?string $uuid = null,
        public readonly ?string $thumbnail = null,
        public readonly ?string $previewUrl = null,
        public readonly ?int $duration = null,
        /** @var list<int> */
        public readonly array $trackIds = [],
        /** @var list<Artist> */
        public readonly array $artists = [],
        /** @var list<string> */
        public readonly array $disclaimers = [],
        public readonly ?bool $explicit = null,
        public readonly ?Cover $cover = null,
        public readonly ?ContentRestrictions $contentRestrictions = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->clipId, $this->uuid];
    }
}
