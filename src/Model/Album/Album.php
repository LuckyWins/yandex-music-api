<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Album;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\ActionButton;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\ContentRestrictions;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\CoverDerivedColors;
use LuckyWins\YandexMusic\Model\CustomWave;
use LuckyWins\YandexMusic\Model\Label\Label;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\Trailer;

/**
 * An album.
 *
 * Two fields resist being declared and are resolved by hand below: `labels`
 * arrives as either objects or plain names depending on the endpoint, and
 * `volumes` is a list per disc rather than a flat track list.
 */
final class Album extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artists' => [Artist::class, 'list'],
        'duplicates' => [self::class, 'list'],
        'albums' => [self::class, 'list'],
        'trackPosition' => [TrackPosition::class, 'one'],
        'deprecation' => [Deprecation::class, 'one'],
        'actionButton' => [ActionButton::class, 'one'],
        'cover' => [Cover::class, 'one'],
        'derivedColors' => [CoverDerivedColors::class, 'one'],
        'contentRestrictions' => [ContentRestrictions::class, 'one'],
        'trailer' => [Trailer::class, 'one'],
        'customWave' => [CustomWave::class, 'one'],
        'pager' => [Pager::class, 'one'],
    ];

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $error = null,
        public readonly ?string $title = null,
        public readonly ?int $trackCount = null,
        /** @var list<Artist> */
        public readonly array $artists = [],
        /**
         * Either full label objects or bare names, depending on the endpoint.
         *
         * @var list<Label>|list<string>
         */
        public readonly array $labels = [],
        public readonly ?bool $available = null,
        public readonly ?bool $availableForPremiumUsers = null,
        public readonly ?string $version = null,
        public readonly ?string $coverUri = null,
        public readonly ?string $contentWarning = null,
        public readonly string|int|null $originalReleaseYear = null,
        public readonly ?string $genre = null,
        public readonly ?string $textColor = null,
        public readonly ?string $shortDescription = null,
        public readonly ?string $description = null,
        public readonly ?bool $isPremiere = null,
        public readonly ?bool $isBanner = null,
        public readonly ?string $metaType = null,
        public readonly ?string $storageDir = null,
        public readonly ?string $ogImage = null,
        /** @var list<mixed>|null */
        public readonly ?array $buy = null,
        public readonly ?bool $recent = null,
        public readonly ?bool $veryImportant = null,
        public readonly ?bool $availableForMobile = null,
        public readonly ?bool $availablePartially = null,
        /** @var list<int>|null Ids of the standout tracks. */
        public readonly ?array $bests = null,
        /** @var list<Album> */
        public readonly array $duplicates = [],
        /** @var list<mixed>|null */
        public readonly ?array $prerolls = null,
        /**
         * Tracks grouped by disc: one inner list per volume.
         *
         * @var list<list<Track>>|null
         */
        public readonly ?array $volumes = null,
        public readonly ?int $year = null,
        public readonly ?string $releaseDate = null,
        public readonly ?string $type = null,
        public readonly ?TrackPosition $trackPosition = null,
        /** @var list<string>|null */
        public readonly ?array $regions = null,
        public readonly ?bool $availableAsRbt = null,
        public readonly ?bool $lyricsAvailable = null,
        public readonly ?bool $rememberPosition = null,
        /** @var list<Album> */
        public readonly array $albums = [],
        public readonly ?int $durationMs = null,
        public readonly ?bool $explicit = null,
        public readonly ?string $startDate = null,
        public readonly ?int $likesCount = null,
        public readonly ?Deprecation $deprecation = null,
        /** @var list<string>|null */
        public readonly ?array $availableRegions = null,
        /** @var list<string>|null */
        public readonly ?array $availableForOptions = null,
        public readonly ?bool $listeningFinished = null,
        /** @var list<string>|null */
        public readonly ?array $disclaimers = null,
        public readonly ?ActionButton $actionButton = null,
        /** The cover as an object; `coverUri` carries the same art as a template. */
        public readonly ?Cover $cover = null,
        public readonly ?CoverDerivedColors $derivedColors = null,
        public readonly ?Trailer $trailer = null,
        public readonly ?bool $hasTrailer = null,
        public readonly ?bool $childContent = null,
        public readonly ?ContentRestrictions $contentRestrictions = null,
        public readonly ?CustomWave $customWave = null,
        /** Present when the album arrives as one page of a longer list. */
        public readonly ?Pager $pager = null,
        public readonly ?string $metaTagId = null,
        public readonly ?string $sortOrder = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Fill in the two fields whose shape the declaration cannot capture.
     */
    protected static function prepare(array $args, array $data, ?Client $client): array
    {
        $rawLabels = $data['labels'] ?? null;

        if (is_array($rawLabels)) {
            $labels = [];

            foreach ($rawLabels as $label) {
                // Some endpoints send the whole label, others only its name.
                $resolved = is_array($label) ? Label::fromApi($label, $client) : $label;

                if (null !== $resolved && (is_string($resolved) || $resolved instanceof Label)) {
                    $labels[] = $resolved;
                }
            }

            $args['labels'] = $labels;
        }

        $rawVolumes = $data['volumes'] ?? null;

        if (is_array($rawVolumes)) {
            $volumes = [];

            // One inner list per disc.
            foreach ($rawVolumes as $volume) {
                $volumes[] = Track::listFromApi($volume, $client);
            }

            $args['volumes'] = $volumes;
        }

        return $args;
    }

    /**
     * Every track on the album, with the disc grouping flattened away.
     *
     * @return list<Track>
     */
    public function tracks(): array
    {
        $tracks = [];

        foreach ($this->volumes ?? [] as $volume) {
            foreach ($volume as $track) {
                $tracks[] = $track;
            }
        }

        return $tracks;
    }

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
