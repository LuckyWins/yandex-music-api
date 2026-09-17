<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Pins as PinsTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Pin\Pin;
use LuckyWins\YandexMusic\Model\Pin\PinsList;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PinsTrait::class)]
final class PinsTest extends TestCase
{
    public function testWhatIsPinned(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'pins' => [['type' => 'album', 'data' => ['id' => 4243617, 'title' => 'Hajime']]],
        ]]);

        $pins = $this->client($http)->pins();

        self::assertInstanceOf(PinsList::class, $pins);
        self::assertCount(1, $pins->pins);
        self::assertSame('https://api.music.yandex.net/pins', (string) $http->lastRequest()->getUri());
    }

    /**
     * Pinning is a PUT with a JSON body; the path says the kind and the body
     * says which one.
     */
    public function testPinningEachKind(): void
    {
        foreach ([
            ['pinAlbum', [4243617], 'album', ['id' => 4243617]],
            ['pinArtist', [4611844], 'artist', ['id' => 4611844]],
            ['pinPlaylist', [503646255, 1042], 'playlist', ['uid' => 503646255, 'kind' => 1042]],
            ['pinWave', ['user:onyourwave'], 'wave', ['seeds' => ['user:onyourwave']]],
        ] as [$method, $arguments, $kind, $body]) {
            $http = (new MockHttpClient())->queue(['result' => ['type' => $kind, 'data' => []]]);

            self::assertInstanceOf(Pin::class, $this->client($http)->{$method}(...$arguments), $method);

            $request = $http->lastRequest();

            self::assertSame('PUT', $request->getMethod(), $method);
            self::assertSame('application/json', $request->getHeaderLine('Content-Type'), $method);
            self::assertSame('https://api.music.yandex.net/pin/'.$kind, (string) $request->getUri(), $method);
            self::assertSame($body, $http->jsonBodyAt(0), $method);
        }
    }

    /**
     * Unpinning is the same body against the same path, as a DELETE — a verb
     * that does not usually carry one.
     */
    public function testUnpinningEachKind(): void
    {
        foreach ([
            ['unpinAlbum', [4243617], 'album', ['id' => 4243617]],
            ['unpinArtist', [4611844], 'artist', ['id' => 4611844]],
            ['unpinPlaylist', [503646255, 1042], 'playlist', ['uid' => 503646255, 'kind' => 1042]],
            ['unpinWave', ['user:onyourwave'], 'wave', ['seeds' => ['user:onyourwave']]],
        ] as [$method, $arguments, $kind, $body]) {
            $http = (new MockHttpClient())->queue(['result' => 'ok']);

            self::assertTrue($this->client($http)->{$method}(...$arguments), $method);

            $request = $http->lastRequest();

            self::assertSame('DELETE', $request->getMethod(), $method);
            self::assertSame('https://api.music.yandex.net/pin/'.$kind, (string) $request->getUri(), $method);
            self::assertSame($body, $http->jsonBodyAt(0), $method);
        }
    }

    /**
     * A bare seed is wrapped rather than sent as a string: a string makes the
     * server hang up without answering at all.
     */
    public function testWaveSeedsAlwaysTravelAsAList(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['type' => 'wave']])
            ->queue(['result' => ['type' => 'wave']]);

        $client = $this->client($http);

        $client->pinWave('user:onyourwave');
        $client->pinWave(['user:onyourwave', 'genre:allrock']);

        self::assertSame(['seeds' => ['user:onyourwave']], $http->jsonBodyAt(0));
        self::assertSame(['seeds' => ['user:onyourwave', 'genre:allrock']], $http->jsonBodyAt(1));
    }

    public function testARefusedUnpinIsReported(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'error']);

        self::assertFalse($this->client($http)->unpinAlbum(4243617));
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
