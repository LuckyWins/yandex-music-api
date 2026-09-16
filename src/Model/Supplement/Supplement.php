<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Supplement;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Extra material attached to a track: videos, a podcast description, and —
 * deprecated — its lyrics.
 */
final class Supplement extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'lyrics' => [Lyrics::class, 'one'],
        'videos' => [VideoSupplement::class, 'list'],
    ];

    public function __construct(
        public readonly int $id,
        public readonly ?Lyrics $lyrics = null,
        /** @var list<VideoSupplement> */
        public readonly array $videos = [],
        public readonly ?bool $radioIsAvailable = null,
        /** The full text for a podcast episode. */
        public readonly ?string $description = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->lyrics, $this->videos];
    }
}
