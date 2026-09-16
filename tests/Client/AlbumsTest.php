<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Albums as AlbumsTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AlbumsTrait::class)]
final class AlbumsTest extends TestCase
{
    public function testAlbumsPostsTheIds(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [['id' => 1, 'title' => 'Hajime']]]);

        $albums = $this->client($http)->albums([1, 2]);

        self::assertCount(1, $albums);
        self::assertInstanceOf(Album::class, $albums[0]);
        self::assertSame('POST', $http->lastRequest()->getMethod());
        self::assertSame('https://api.music.yandex.net/albums', (string) $http->lastRequest()->getUri());
        self::assertSame(['album-ids' => '1,2'], $http->formBodyAt(0));
    }

    public function testAlbumFetchesOneWithoutItsTracks(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['id' => 1, 'title' => 'Hajime', 'trackCount' => 12]]);

        $album = $this->client($http)->album(1);

        self::assertInstanceOf(Album::class, $album);
        self::assertSame(12, $album->trackCount);
        self::assertNull($album->volumes, 'this endpoint carries no tracks');
        self::assertSame('https://api.music.yandex.net/albums/1', (string) $http->lastRequest()->getUri());
    }

    /**
     * The shape this endpoint returns is the reason the whole Album tree had
     * to be ported before this domain could be.
     */
    public function testAlbumsWithTracksCarriesTheDiscs(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'id' => 33431736,
            'title' => 'Hajime, Pt. 2',
            'artists' => [['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']],
            'labels' => ['Hajime'],
            'volumes' => [
                [['id' => 1, 'title' => 'Люби меня'], ['id' => 2, 'title' => 'Второй']],
                [['id' => 3, 'title' => 'Со второго диска']],
            ],
        ]]);

        $album = $this->client($http)->albumsWithTracks(33431736);

        self::assertInstanceOf(Album::class, $album);
        self::assertCount(2, $album->volumes ?? []);
        self::assertCount(3, $album->tracks());
        self::assertInstanceOf(Track::class, $album->tracks()[0]);
        self::assertSame('Люби меня', $album->tracks()[0]->title);
        self::assertSame(['Hajime'], $album->labels);
        self::assertSame(
            'https://api.music.yandex.net/albums/33431736/with-tracks',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * A list, despite the singular path — and empty for most albums.
     */
    public function testDisclaimersComeBackAsAList(): void
    {
        $http = (new MockHttpClient())->queue(['result' => []]);

        self::assertSame([], $this->client($http)->albumsDisclaimer(1));
        self::assertSame('https://api.music.yandex.net/albums/1/disclaimer', (string) $http->lastRequest()->getUri());
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
