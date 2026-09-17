<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Search;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\Video;

/**
 * What a search found.
 *
 * A search for one type fills that set and leaves the rest null; a search for
 * everything fills whichever sets had matches. $best is the single strongest
 * match across all of them.
 *
 * When the query looked like a typo, the service searches for what it thinks
 * you meant and says so in the three misspell fields.
 */
final class Search extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'best' => [Best::class, 'one'],
    ];

    /**
     * Which model each result set holds. Podcasts are albums and their
     * episodes are tracks, which is the API's arrangement, not ours.
     *
     * @var array<string, array{0: class-string<Model>, 1: string}>
     */
    private const SETS = [
        'albums' => [Album::class, 'album'],
        'artists' => [Artist::class, 'artist'],
        'playlists' => [Playlist::class, 'playlist'],
        'tracks' => [Track::class, 'track'],
        'videos' => [Video::class, 'video'],
        'clips' => [Clip::class, 'clip'],
        'users' => [User::class, 'user'],
        'podcasts' => [Album::class, 'podcast'],
        'podcastEpisodes' => [Track::class, 'podcast_episode'],
    ];

    public function __construct(
        public readonly ?string $searchRequestId = null,
        public readonly ?string $text = null,
        public readonly ?Best $best = null,
        /** @var SearchResult<Album>|null */
        public readonly ?SearchResult $albums = null,
        /** @var SearchResult<Artist>|null */
        public readonly ?SearchResult $artists = null,
        /** @var SearchResult<Playlist>|null */
        public readonly ?SearchResult $playlists = null,
        /** @var SearchResult<Track>|null */
        public readonly ?SearchResult $tracks = null,
        /** @var SearchResult<Video>|null */
        public readonly ?SearchResult $videos = null,
        /**
         * Clips, which arrive without a type of their own — the only set that
         * relies on the field name to say what it holds.
         *
         * @var SearchResult<Clip>|null
         */
        public readonly ?SearchResult $clips = null,
        /** @var SearchResult<User>|null */
        public readonly ?SearchResult $users = null,
        /** @var SearchResult<Album>|null */
        public readonly ?SearchResult $podcasts = null,
        /** @var SearchResult<Track>|null */
        public readonly ?SearchResult $podcastEpisodes = null,
        public readonly ?string $type = null,
        public readonly ?int $page = null,
        public readonly ?int $perPage = null,
        public readonly ?string $misspellResult = null,
        public readonly ?string $misspellOriginal = null,
        public readonly ?bool $misspellCorrected = null,
        public readonly ?bool $nocorrect = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Every result set found, keyed by field name, skipping the empty ones.
     *
     * @return array<string, SearchResult<Model>>
     */
    public function sets(): array
    {
        $found = [];

        foreach (array_keys(self::SETS) as $name) {
            /** @var SearchResult<Model>|null $set */
            $set = $this->{$name};

            if (null !== $set && [] !== $set->results) {
                $found[$name] = $set;
            }
        }

        return $found;
    }

    /**
     * Result sets cannot be declared through NESTED: each holds a different
     * model, and the response says which in its own `type` rather than in the
     * key it arrived under.
     */
    protected static function prepare(array $args, array $data, ?Client $client): array
    {
        // The response spells this one `podcast_episodes`, so the raw keys are
        // matched the same way the rest of deserialization matches them.
        $byKey = [];

        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $byKey[self::canonical($key)] = $value;
            }
        }

        foreach (self::SETS as $name => [$model, $expectedType]) {
            $args[$name] = SearchResult::of($model, $expectedType, $byKey[self::canonical($name)] ?? null, $client);
        }

        return $args;
    }

    protected function identity(): array
    {
        return [$this->searchRequestId, $this->text, $this->type, $this->page];
    }
}
