<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Pin;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\ContentRestrictions;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Whatever was pinned, in the little the front page needs to draw it.
 *
 * One shape for all four kinds: an album or artist fills $id and $title or
 * $name, a playlist fills $uid with $kind, and a wave fills neither. The
 * pin's own type says which to read.
 */
final class PinData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'cover' => [Cover::class, 'one'],
        'contentRestrictions' => [ContentRestrictions::class, 'one'],
    ];

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $uid = null,
        public readonly ?int $kind = null,
        public readonly ?string $playlistUuid = null,
        public readonly ?string $name = null,
        public readonly ?string $title = null,
        public readonly ?Cover $cover = null,
        public readonly ?ContentRestrictions $contentRestrictions = null,
        public readonly ?string $contentWarning = null,
        /**
         * A pinned wave describes itself rather than pointing at something:
         * these five are what it fills instead of an id.
         */
        public readonly ?string $header = null,
        public readonly ?string $animationUrl = null,
        public readonly ?string $backgroundImageUrl = null,
        public readonly ?string $stationId = null,
        /** @var list<string> what the wave is built from */
        public readonly array $seeds = [],
        /** @var array<string, mixed> Not modelled: shape not captured yet. */
        public readonly array $colors = [],
        /** @var array<string, mixed> Not modelled: looks like a wave agent. */
        public readonly array $agent = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->uid, $this->kind];
    }
}
