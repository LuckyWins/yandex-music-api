<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Supplement;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A video tied to a track, usually its official clip.
 */
final class VideoSupplement extends Model
{
    public function __construct(
        public readonly string $cover,
        public readonly string $provider,
        public readonly ?string $title = null,
        public readonly ?string $providerVideoId = null,
        public readonly ?string $url = null,
        /** Hosted by Yandex rather than the provider. */
        public readonly ?string $embedUrl = null,
        /** Ready-made HTML for embedding. */
        public readonly ?string $embed = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->cover, $this->title, $this->providerVideoId];
    }
}
