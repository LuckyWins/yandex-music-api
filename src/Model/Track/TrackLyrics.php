<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A pointer to a track's lyrics — the text itself is not in the response.
 *
 * `downloadUrl` is a presigned link with its own expiry; fetch it plainly,
 * without this library's authorization header.
 */
final class TrackLyrics extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'major' => [LyricsMajor::class, 'one'],
    ];

    public function __construct(
        public readonly string $downloadUrl,
        public readonly int $lyricId,
        public readonly string $externalLyricId,
        /** @var list<string> */
        public readonly array $writers,
        public readonly ?LyricsMajor $major = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->lyricId, $this->externalLyricId];
    }
}
