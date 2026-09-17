<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Search;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\Video;

/**
 * What to search for, and what a result of that kind deserializes into.
 *
 * The API takes these as plain strings; spelling one wrong is answered with an
 * empty result rather than an error, which is why they are an enum here.
 */
enum SearchType: string
{
    case All = 'all';
    case Track = 'track';
    case Album = 'album';
    case Artist = 'artist';
    case Playlist = 'playlist';
    case Video = 'video';
    case User = 'user';
    case Clip = 'clip';
    case Podcast = 'podcast';
    case PodcastEpisode = 'podcast_episode';

    /**
     * The model a result of this type is built from.
     *
     * Podcasts are albums and their episodes are tracks — the API says so, not
     * us. `all` has no single model, because it is not a result type.
     *
     * @return class-string<Model>|null
     */
    public function model(): ?string
    {
        return match ($this) {
            self::Track, self::PodcastEpisode => Track::class,
            self::Album, self::Podcast => Album::class,
            self::Artist => Artist::class,
            self::Playlist => Playlist::class,
            self::Video => Video::class,
            self::Clip => Clip::class,
            self::User => User::class,
            self::All => null,
        };
    }
}
