<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * A music video, as search returns it.
 *
 * Not the same thing as Supplement\VideoSupplement: that one describes how to
 * embed a player for a track's video, this one describes the video itself.
 * The reference library keeps them apart too.
 */
final class Video extends Model
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $cover = null,
        public readonly ?string $embedUrl = null,
        public readonly ?string $provider = null,
        public readonly string|int|null $providerVideoId = null,
        public readonly ?string $youtubeUrl = null,
        public readonly ?string $thumbnailUrl = null,
        public readonly ?int $duration = null,
        public readonly ?string $text = null,
        public readonly ?string $htmlAutoPlayVideoPlayer = null,
        /** @var list<string> */
        public readonly array $regions = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->provider, $this->providerVideoId, $this->title];
    }
}
