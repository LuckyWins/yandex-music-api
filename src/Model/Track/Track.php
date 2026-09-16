<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\CoverDerivedColors;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\User;

/**
 * A track.
 *
 * The widest model in the library, and the one most responses are made of.
 *
 * Two identifiers matter and they are not interchangeable. `id` names the
 * recording; `compositeId()` pairs it with an album and is what the endpoints
 * dealing with playback and likes expect. Getting them the wrong way round
 * fails quietly, returning nothing rather than erroring.
 *
 * Fields from `canPublish` through `userInfo` appear only on tracks a user
 * uploaded themselves. Podcasts normally have `rememberPosition` set, ordinary
 * tracks do not.
 */
final class Track extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artists' => [Artist::class, 'list'],
        'albums' => [Album::class, 'list'],
        'poetryLoverMatches' => [PoetryLoverMatch::class, 'list'],
        'major' => [Major::class, 'one'],
        'substituted' => [self::class, 'one'],
        'matchedTrack' => [self::class, 'one'],
        'normalization' => [Normalization::class, 'one'],
        'userInfo' => [User::class, 'one'],
        'metaData' => [MetaData::class, 'one'],
        'r128' => [R128::class, 'one'],
        'lyricsInfo' => [LyricsInfo::class, 'one'],
        'derivedColors' => [CoverDerivedColors::class, 'one'],
        'fade' => [Fade::class, 'one'],
        'smartPreviewParams' => [SmartPreviewParams::class, 'one'],
    ];

    public function __construct(
        public readonly string|int $id,
        public readonly ?string $title = null,
        public readonly ?bool $available = null,
        /** @var list<Artist> */
        public readonly array $artists = [],
        /** @var list<Album> */
        public readonly array $albums = [],
        public readonly ?bool $availableForPremiumUsers = null,
        public readonly ?bool $lyricsAvailable = null,
        /** @var list<PoetryLoverMatch> */
        public readonly array $poetryLoverMatches = [],
        public readonly ?bool $best = null,
        public readonly string|int|null $realId = null,
        public readonly ?string $ogImage = null,
        /** Known value: `music`. */
        public readonly ?string $type = null,
        public readonly ?string $coverUri = null,
        public readonly ?Major $major = null,
        public readonly ?int $durationMs = null,
        public readonly ?string $storageDir = null,
        public readonly ?int $fileSize = null,
        /** What plays instead, where the original is unavailable here. */
        public readonly ?self $substituted = null,
        public readonly ?self $matchedTrack = null,
        public readonly ?Normalization $normalization = null,
        public readonly ?string $error = null,
        public readonly ?bool $canPublish = null,
        public readonly ?string $state = null,
        public readonly ?string $desiredVisibility = null,
        public readonly ?string $filename = null,
        public readonly ?User $userInfo = null,
        public readonly ?MetaData $metaData = null,
        /** @var list<string>|null */
        public readonly ?array $regions = null,
        public readonly ?bool $availableAsRbt = null,
        /** Known value: `explicit`. */
        public readonly ?string $contentWarning = null,
        public readonly ?bool $explicit = null,
        public readonly ?int $previewDurationMs = null,
        public readonly ?bool $availableFullWithoutPermission = null,
        public readonly ?string $version = null,
        public readonly ?bool $rememberPosition = null,
        public readonly ?string $backgroundVideoUri = null,
        public readonly ?string $shortDescription = null,
        public readonly ?bool $isSuitableForChildren = null,
        /** Known values: `OWN`, `OWN_REPLACED_TO_UGC`. */
        public readonly ?string $trackSource = null,
        /** @var list<string>|null Known value: `bookmate`. */
        public readonly ?array $availableForOptions = null,
        public readonly ?R128 $r128 = null,
        public readonly ?LyricsInfo $lyricsInfo = null,
        /** Known values: `VIDEO_ALLOWED`, `COVER_ONLY`. */
        public readonly ?string $trackSharingFlag = null,
        public readonly ?CoverDerivedColors $derivedColors = null,
        public readonly ?Fade $fade = null,
        public readonly ?SmartPreviewParams $smartPreviewParams = null,
        /** @var list<string>|null */
        public readonly ?array $specialAudioResources = null,
        /** @var list<string>|null */
        public readonly ?array $disclaimers = null,
        public readonly ?string $backgroundVideoId = null,
        public readonly ?string $playerId = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The identifier the playback and likes endpoints expect: the track paired
     * with the album it is being played from.
     *
     * Falls back to the bare id when no album is attached, which is what the
     * reference library does too.
     */
    public function compositeId(): string
    {
        $album = $this->albums[0] ?? null;

        return null === $album ? (string) $this->id : $this->id.':'.$album->id;
    }

    /**
     * The names of everyone credited on the track.
     *
     * @return list<string>
     */
    public function artistNames(): array
    {
        $names = [];

        foreach ($this->artists as $artist) {
            if (null !== $artist->name) {
                $names[] = $artist->name;
            }
        }

        return $names;
    }

    /**
     * The cover at a given size, or null when the track has no art.
     */
    public function coverUrl(string $size = '200x200'): ?string
    {
        if (null === $this->coverUri) {
            return null;
        }

        return 'https://'.str_replace('%%', $size, $this->coverUri);
    }

    protected function identity(): array
    {
        return [$this->id];
    }
}
