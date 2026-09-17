<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Concerts as ConcertsTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Concert\ConcertFeed;
use LuckyWins\YandexMusic\Model\Concert\ConcertInfo;
use LuckyWins\YandexMusic\Model\Concert\ConcertLocations;
use LuckyWins\YandexMusic\Model\Concert\ConcertSkeleton;
use LuckyWins\YandexMusic\Model\Concert\ConcertTabConfig;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConcertsTrait::class)]
final class ConcertsTest extends TestCase
{
    public function testTheCities(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'locations' => [['id' => 213, 'name' => 'Москва']],
        ]]);

        $locations = $this->client($http)->concertsLocations();

        self::assertInstanceOf(ConcertLocations::class, $locations);
        self::assertSame(213, $locations->idOf('Москва'));
        self::assertSame('https://api.music.yandex.net/concerts/locations', (string) $http->lastRequest()->getUri());
    }

    public function testTheTabConfiguration(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'config' => ['top' => ['offset' => 0, 'limit' => 5], 'feed' => ['offset' => 5, 'limit' => -1]],
        ]]);

        $config = $this->client($http)->concertsTabConfig();

        self::assertInstanceOf(ConcertTabConfig::class, $config);
        self::assertSame(5, $config->config?->top?->limit);
        self::assertSame('https://api.music.yandex.net/concerts/tab-config', (string) $http->lastRequest()->getUri());
    }

    /**
     * Cities go comma-separated, and no cities means no parameter at all
     * rather than an empty one.
     */
    public function testTheListingWithAndWithoutCities(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['items' => []]])
            ->queue(['result' => ['items' => []]])
            ->queue(['result' => ['items' => []]]);

        $client = $this->client($http);

        $client->concertsFeed();
        $client->concertsFeed([]);
        $client->concertsFeed([213, 2]);

        self::assertSame('https://api.music.yandex.net/concerts/feed', (string) $http->requestAt(0)->getUri());
        self::assertSame('https://api.music.yandex.net/concerts/feed', (string) $http->requestAt(1)->getUri());
        self::assertSame('locations=213%2C2', $http->requestAt(2)->getUri()->getQuery());
    }

    public function testTheListingIsTyped(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'items' => [[
                'type' => 'concert_item',
                'data' => ['concert' => ['id' => 'c1', 'concertTitle' => 'Boulevard Depo']],
            ]],
        ]]);

        $feed = $this->client($http)->concertsFeed([213]);

        self::assertInstanceOf(ConcertFeed::class, $feed);
        self::assertCount(1, $feed->concerts());
        self::assertSame('Boulevard Depo', $feed->concerts()[0]->concertTitle);
    }

    public function testOneConcertsPage(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'concert' => ['id' => 'c1', 'concertTitle' => 'Boulevard Depo'],
            'leadArtistId' => 4611844,
        ]]);

        $info = $this->client($http)->concertInfo('c1');

        self::assertInstanceOf(ConcertInfo::class, $info);
        self::assertSame(4611844, $info->leadArtistId);
        self::assertSame('https://api.music.yandex.net/concerts/c1/info', (string) $http->lastRequest()->getUri());
    }

    /**
     * The layout's second identifier has a documented default here, unlike
     * the artist one.
     */
    public function testTheLayoutAndItsDefault(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['id' => 'concert_page', 'blocks' => []]])
            ->queue(['result' => ['id' => 'other', 'blocks' => []]]);

        $client = $this->client($http);

        self::assertInstanceOf(ConcertSkeleton::class, $client->concertSkeleton('c1'));
        self::assertSame(
            'https://api.music.yandex.net/concerts/c1/skeletons/concert_page',
            (string) $http->requestAt(0)->getUri(),
        );

        $client->concertSkeleton('c1', 'other');

        self::assertSame(
            'https://api.music.yandex.net/concerts/c1/skeletons/other',
            (string) $http->requestAt(1)->getUri(),
        );
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
