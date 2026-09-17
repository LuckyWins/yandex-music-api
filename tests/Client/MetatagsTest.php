<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Metatags as MetatagsTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Metatag\Metatag;
use LuckyWins\YandexMusic\Model\Metatag\MetatagAlbums;
use LuckyWins\YandexMusic\Model\Metatag\MetatagArtists;
use LuckyWins\YandexMusic\Model\Metatag\MetatagPlaylists;
use LuckyWins\YandexMusic\Model\Metatag\Metatags;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MetatagsTrait::class)]
final class MetatagsTest extends TestCase
{
    public function testTheTreeOfTags(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'trees' => [['navigationId' => 'mood', 'leaves' => [['tag' => 'run']]]],
        ]]);

        $tags = $this->client($http)->metatags();

        self::assertInstanceOf(Metatags::class, $tags);
        self::assertCount(1, $tags->tags());
        self::assertSame('https://api.music.yandex.net/landing3/metatags', (string) $http->lastRequest()->getUri());
    }

    /**
     * Eleven optional parameters, and what was not asked for must not be sent
     * — an empty `tracksCount` is not the same as none.
     */
    public function testOnlyWhatWasAskedForTravels(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['id' => 'run']])
            ->queue(['result' => ['id' => 'run']]);

        $client = $this->client($http);

        $client->metatag('run');

        self::assertSame('https://api.music.yandex.net/metatags/run', (string) $http->requestAt(0)->getUri());

        $client->metatag('run', tracksCount: 5, albumsSortBy: 'new', withLikesCount: true);

        self::assertSame(
            'tracksCount=5&albumsSortBy=new&withLikesCount=true',
            $http->requestAt(1)->getUri()->getQuery(),
        );
    }

    public function testTheAlbumsOfATag(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'id' => 'run',
            'albums' => [['id' => 4243617, 'title' => 'Hajime']],
        ]]);

        $albums = $this->client($http)->metatagAlbums('run', 20, 10, period: 'month', sortBy: 'popular');

        self::assertInstanceOf(MetatagAlbums::class, $albums);
        self::assertCount(1, $albums->albums);
        self::assertSame(
            'https://api.music.yandex.net/metatags/run/albums?offset=20&limit=10&period=month&sortBy=popular',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testTheArtistsOfATag(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'id' => 'run',
            'artists' => [['artist' => ['id' => 4611844, 'name' => 'Miyagi']]],
        ]]);

        $artists = $this->client($http)->metatagArtists('run', tracksPerArtist: 3);

        self::assertInstanceOf(MetatagArtists::class, $artists);
        self::assertSame('Miyagi', $artists->artists[0]->artist?->name);
        self::assertSame(
            'https://api.music.yandex.net/metatags/run/artists?period=month&offset=0&limit=20&tracksPerArtist=3',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testThePlaylistsOfATag(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['id' => 'run', 'playlists' => [['kind' => 1042]]]]);

        $playlists = $this->client($http)->metatagPlaylists('run');

        self::assertInstanceOf(MetatagPlaylists::class, $playlists);
        self::assertSame(
            'https://api.music.yandex.net/metatags/run/playlists?offset=0&limit=20',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * A tag can be named in Cyrillic, and a path is not a place to paste a
     * string into unencoded — the service answers "not found" with the name
     * mangled, which reads like the tag is wrong rather than the request.
     */
    public function testATagIsEncodedIntoThePath(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['id' => 'осень']])
            ->queue(['result' => ['id' => 'осень']]);

        $client = $this->client($http);

        $client->metatag('осенняя музыка');

        self::assertSame(
            'https://api.music.yandex.net/metatags/%D0%BE%D1%81%D0%B5%D0%BD%D0%BD%D1%8F%D1%8F%20%D0%BC%D1%83%D0%B7%D1%8B%D0%BA%D0%B0',
            (string) $http->requestAt(0)->getUri(),
        );

        $client->metatagAlbums('осень');

        self::assertStringContainsString('/metatags/%D0%BE', (string) $http->requestAt(1)->getUri());
    }

    public function testATagsPageIsTyped(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'id' => 'run',
            'title' => ['title' => 'Для бега'],
            'stationId' => 'tag:run',
        ]]);

        $tag = $this->client($http)->metatag('run');

        self::assertInstanceOf(Metatag::class, $tag);
        self::assertSame('Для бега', $tag->title?->title);
        self::assertSame('tag:run', $tag->stationId);
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
