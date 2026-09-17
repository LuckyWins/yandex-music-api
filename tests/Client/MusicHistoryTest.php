<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\MusicHistory as MusicHistoryTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistory;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryItems;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryQuery;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MusicHistoryTrait::class)]
final class MusicHistoryTest extends TestCase
{
    public function testTheHistoryByDay(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'historyTabs' => [['date' => '2026-09-17', 'items' => []]],
        ]]);

        $history = $this->client($http)->musicHistory(3);

        self::assertInstanceOf(MusicHistory::class, $history);
        self::assertCount(1, $history->historyTabs);
        self::assertSame(
            'https://api.music.yandex.net/music-history?fullModelsCount=3',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * Filling entries in is a POST with a JSON body, built by the query
     * rather than by five parallel lists.
     */
    public function testFillingEntriesIn(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'items' => [['type' => 'track', 'data' => ['itemId' => ['trackId' => '31190260']]]],
        ]]);

        $items = $this->client($http)->musicHistoryItems(
            (new MusicHistoryQuery())->track(31190260, 4243617)->album(4243617),
        );

        self::assertInstanceOf(MusicHistoryItems::class, $items);
        self::assertCount(1, $items->items);

        $request = $http->lastRequest();

        self::assertSame('POST', $request->getMethod());
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame('https://api.music.yandex.net/music-history/items', (string) $request->getUri());
        self::assertSame([
            'items' => [
                ['type' => 'track', 'data' => ['itemId' => ['trackId' => '31190260', 'albumId' => '4243617']]],
                ['type' => 'album', 'data' => ['itemId' => ['id' => '4243617']]],
            ],
        ], $http->jsonBodyAt(0));
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
