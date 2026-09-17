<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Landing as LandingTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Feed\Feed;
use LuckyWins\YandexMusic\Model\Genre\Genre;
use LuckyWins\YandexMusic\Model\Landing\BlockType;
use LuckyWins\YandexMusic\Model\Landing\ChartInfo;
use LuckyWins\YandexMusic\Model\Landing\Landing;
use LuckyWins\YandexMusic\Model\Landing\LandingList;
use LuckyWins\YandexMusic\Model\Playlist\TagResult;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LandingTrait::class)]
final class LandingTest extends TestCase
{
    public function testTheFeedIsTyped(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'today' => '2026-09-17',
            'days' => [['day' => '2026-09-17', 'events' => [['id' => 'e1', 'type' => 'promotion']]]],
        ]]);

        $feed = $this->client($http)->feed();

        self::assertInstanceOf(Feed::class, $feed);
        self::assertCount(1, $feed->days);
        self::assertSame('https://api.music.yandex.net/feed', (string) $http->lastRequest()->getUri());
    }

    public function testTheWizardFlagIsUnwrapped(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['isWizardPassed' => true]])
            ->queue(['result' => ['isWizardPassed' => false]])
            ->queue(['result' => []]);

        $client = $this->client($http);

        self::assertTrue($client->feedWizardIsPassed());
        self::assertFalse($client->feedWizardIsPassed());
        self::assertFalse($client->feedWizardIsPassed(), 'an answer without the flag is not a yes');
        self::assertSame(
            'https://api.music.yandex.net/feed/wizard/is-passed',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * Blocks can be named one at a time or several at once, typed or bare.
     */
    public function testTheBlocksAskedFor(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['contentId' => 'c1']])
            ->queue(['result' => ['contentId' => 'c1']])
            ->queue(['result' => ['contentId' => 'c1']]);

        $client = $this->client($http);

        $client->landing(BlockType::Chart);
        $client->landing('chart');
        $client->landing([BlockType::Chart, 'personalplaylists', BlockType::Mixes]);

        self::assertSame('blocks=chart', $http->requestAt(0)->getUri()->getQuery());
        self::assertSame('blocks=chart', $http->requestAt(1)->getUri()->getQuery());
        self::assertSame(
            'blocks=chart%2Cpersonalplaylists%2Cmixes',
            $http->requestAt(2)->getUri()->getQuery(),
        );
    }

    /**
     * The reference library pins a stranger's user id into every landing
     * request. Ours sends one only when asked to.
     */
    public function testNoStrangersUserIdIsSentUnlessAskedFor(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['contentId' => 'c1']])
            ->queue(['result' => ['contentId' => 'c1']]);

        $client = $this->client($http);

        $landing = $client->landing(BlockType::Chart);

        self::assertInstanceOf(Landing::class, $landing);
        self::assertStringNotContainsString('eitherUserId', $http->requestAt(0)->getUri()->getQuery());

        $client->landing(BlockType::Chart, eitherUserId: '10254713668400548221');

        self::assertSame(
            'blocks=chart&eitherUserId=10254713668400548221',
            $http->requestAt(1)->getUri()->getQuery(),
        );
    }

    public function testTheChartWithAndWithoutAnOption(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['id' => 'chart', 'title' => 'Чарт']])
            ->queue(['result' => ['id' => 'chart-world', 'title' => 'Мир']])
            ->queue(['result' => ['id' => 'chart', 'title' => 'Чарт']]);

        $client = $this->client($http);

        $chart = $client->chart();

        self::assertInstanceOf(ChartInfo::class, $chart);
        self::assertSame('https://api.music.yandex.net/landing3/chart', (string) $http->requestAt(0)->getUri());

        $client->chart('world');

        self::assertSame('https://api.music.yandex.net/landing3/chart/world', (string) $http->requestAt(1)->getUri());

        $client->chart('');

        self::assertSame(
            'https://api.music.yandex.net/landing3/chart',
            (string) $http->requestAt(2)->getUri(),
            'an empty option is no option',
        );
    }

    public function testTheThreeListings(): void
    {
        foreach ([
            ['newReleases', 'new-releases', ['newReleases' => [4243617]]],
            ['newPlaylists', 'new-playlists', ['newPlaylists' => [['uid' => 1, 'kind' => 1042]]]],
            ['podcasts', 'podcasts', ['podcasts' => [99]]],
        ] as [$method, $path, $payload]) {
            $http = (new MockHttpClient())->queue(['result' => ['type' => $path] + $payload]);

            $list = $this->client($http)->{$method}();

            self::assertInstanceOf(LandingList::class, $list, $method);
            self::assertSame(
                'https://api.music.yandex.net/landing3/'.$path,
                (string) $http->lastRequest()->getUri(),
                $method,
            );
        }
    }

    public function testGenresAreAList(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            ['id' => 'all', 'title' => 'Все жанры'],
            ['id' => 'rock', 'title' => 'Рок', 'subGenres' => [['id' => 'punk', 'title' => 'Панк']]],
        ]]);

        $genres = $this->client($http)->genres();

        self::assertCount(2, $genres);
        self::assertInstanceOf(Genre::class, $genres[1]);
        self::assertCount(1, $genres[1]->subGenres);
        self::assertSame('https://api.music.yandex.net/genres', (string) $http->lastRequest()->getUri());
    }

    public function testTagsAreReferencesToPlaylists(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'tag' => 'rock',
            'ids' => [['uid' => 503646255, 'kind' => 1042]],
        ]]);

        $result = $this->client($http)->tags('rock');

        self::assertInstanceOf(TagResult::class, $result);
        self::assertSame(['503646255:1042'], $result->pairs());
        self::assertSame(
            'https://api.music.yandex.net/tags/rock/playlist-ids',
            (string) $http->lastRequest()->getUri(),
        );
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
