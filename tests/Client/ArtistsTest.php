<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Artists as ArtistsTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Artist\ArtistAlbums;
use LuckyWins\YandexMusic\Model\Artist\ArtistTracks;
use LuckyWins\YandexMusic\Model\Artist\BriefInfo;
use LuckyWins\YandexMusic\Model\Artist\SimilarArtists;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArtistsTrait::class)]
final class ArtistsTest extends TestCase
{
    public function testArtistsPostsTheIds(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [['id' => 1, 'name' => 'KREC']]]);

        $artists = $this->client($http)->artists(1);

        self::assertCount(1, $artists);
        self::assertInstanceOf(Artist::class, $artists[0]);
        self::assertSame(['artist-ids' => '1'], $http->formBodyAt(0));
    }

    /**
     * Paging is the same everywhere in this domain: a page and a size going
     * out, a total coming back.
     */
    public function testTracksArePaged(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'tracks' => [['id' => 1, 'title' => 'Люби меня']],
            'pager' => ['total' => 88, 'page' => 1, 'perPage' => 3],
        ]]);

        $tracks = $this->client($http)->artistsTracks(4611844, 1, 3);

        self::assertInstanceOf(ArtistTracks::class, $tracks);
        self::assertCount(1, $tracks->tracks);
        self::assertSame(88, $tracks->pager?->total);

        parse_str($http->lastRequest()->getUri()->getQuery(), $query);
        self::assertSame('1', $query['page'] ?? null);
        self::assertSame('3', $query['page-size'] ?? null);
    }

    /**
     * Albums an artist made and albums they merely appear on are two listings
     * differing only in their path.
     */
    public function testDirectAndAlsoAlbumsHitDifferentPaths(): void
    {
        $page = ['result' => ['albums' => [['id' => 1]], 'pager' => ['total' => 28, 'page' => 0, 'perPage' => 20]]];
        $http = (new MockHttpClient())->queue($page)->queue($page);

        $client = $this->client($http);

        self::assertInstanceOf(ArtistAlbums::class, $client->artistsDirectAlbums(4611844));
        self::assertInstanceOf(ArtistAlbums::class, $client->artistsAlsoAlbums(4611844));

        self::assertStringContainsString('/artists/4611844/direct-albums', (string) $http->requestAt(0)->getUri());
        self::assertStringContainsString('/artists/4611844/also-albums', (string) $http->requestAt(1)->getUri());

        parse_str($http->requestAt(0)->getUri()->getQuery(), $query);
        self::assertSame('year', $query['sort-by'] ?? null);
    }

    public function testSimilarArtists(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'artist' => ['id' => 1, 'name' => 'a'],
            'similarArtists' => [['id' => 2, 'name' => 'b']],
        ]]);

        $similar = $this->client($http)->artistsSimilar(1);

        self::assertInstanceOf(SimilarArtists::class, $similar);
        self::assertCount(1, $similar->similarArtists);
        self::assertSame('https://api.music.yandex.net/artists/1/similar', (string) $http->lastRequest()->getUri());
    }

    /**
     * Bare ids, for when the tracks themselves are not wanted yet.
     */
    public function testTrackIdsByRating(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['artist' => ['id' => 1], 'tracks' => [10, 20, 30]]]);

        self::assertSame([10, 20, 30], $this->client($http)->artistsTrackIdsByRating(1));
    }

    public function testBriefInfoIsTyped(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'artist' => ['id' => 1, 'name' => 'a'],
            'albums' => [['id' => 2]],
            'stats' => ['lastMonthListeners' => 100, 'lastMonthListenersDelta' => 5],
        ]]);

        $brief = $this->client($http)->artistsBriefInfo(1);

        self::assertInstanceOf(BriefInfo::class, $brief);
        self::assertSame('a', $brief->artist?->name);
        self::assertSame(100, $brief->stats?->lastMonthListeners);
        self::assertSame('https://api.music.yandex.net/artists/1/brief-info', (string) $http->lastRequest()->getUri());
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
