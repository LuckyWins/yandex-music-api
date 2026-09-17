<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Search;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Model\Search\SearchType;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\Video;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchType::class)]
final class SearchTypeTest extends TestCase
{
    /**
     * The wire values are the API's, not ours: a renamed case would silently
     * change what gets searched for.
     */
    public function testTheWireValues(): void
    {
        self::assertSame('all', SearchType::All->value);
        self::assertSame('track', SearchType::Track->value);
        self::assertSame('album', SearchType::Album->value);
        self::assertSame('artist', SearchType::Artist->value);
        self::assertSame('playlist', SearchType::Playlist->value);
        self::assertSame('video', SearchType::Video->value);
        self::assertSame('user', SearchType::User->value);
        self::assertSame('clip', SearchType::Clip->value);
        self::assertSame('podcast', SearchType::Podcast->value);
        self::assertSame('podcast_episode', SearchType::PodcastEpisode->value);
    }

    /**
     * Podcasts are albums and their episodes are tracks — the API's
     * arrangement, and the reason these two share models.
     */
    public function testEachTypeNamesItsModel(): void
    {
        self::assertSame(Track::class, SearchType::Track->model());
        self::assertSame(Track::class, SearchType::PodcastEpisode->model());
        self::assertSame(Album::class, SearchType::Album->model());
        self::assertSame(Album::class, SearchType::Podcast->model());
        self::assertSame(Artist::class, SearchType::Artist->model());
        self::assertSame(Playlist::class, SearchType::Playlist->model());
        self::assertSame(Video::class, SearchType::Video->model());
        self::assertSame(Clip::class, SearchType::Clip->model());
        self::assertSame(User::class, SearchType::User->model());
    }

    /**
     * `all` is a request, not a result type, so it has nothing to build.
     */
    public function testAllHasNoModel(): void
    {
        self::assertNull(SearchType::All->model());
    }

    public function testUnknownStringsAreNotTypes(): void
    {
        self::assertNull(SearchType::tryFrom('trak'));
        self::assertSame(SearchType::Track, SearchType::tryFrom('track'));
    }
}
