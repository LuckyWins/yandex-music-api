<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Queues as QueuesTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Queue\Context;
use LuckyWins\YandexMusic\Model\Queue\Queue;
use LuckyWins\YandexMusic\Model\Queue\QueueItem;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(QueuesTrait::class)]
final class QueuesTest extends TestCase
{
    public function testTheListingUnwrapsTheQueues(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'queues' => [['id' => 'q1', 'context' => ['type' => 'playlist']]],
        ]]);

        $queues = $this->client($http)->queuesList();

        self::assertCount(1, $queues);
        self::assertInstanceOf(QueueItem::class, $queues[0]);
        self::assertSame('https://api.music.yandex.net/queues', (string) $http->lastRequest()->getUri());
    }

    /**
     * A queue belongs to a device, so the device travels with the request —
     * and only with that request. The reference writes it into the client's
     * shared headers instead, where it then goes out with everything after.
     */
    public function testTheDeviceHeaderIsSentAndNotKept(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['queues' => []]])
            ->queue(['result' => [['id' => 1, 'title' => 'Hajime']]]);

        $client = $this->client($http);

        $client->queuesList();
        $client->albums(1);

        self::assertStringContainsString('model=Yandex Music API', $http->requestAt(0)->getHeaderLine('X-Yandex-Music-Device'));
        self::assertSame('', $http->requestAt(1)->getHeaderLine('X-Yandex-Music-Device'), 'it must not linger');
    }

    public function testADeviceOfYourOwn(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['queues' => []]]);

        $this->client($http)->queuesList('os=iOS; model=iPhone');

        self::assertSame('os=iOS; model=iPhone', $http->lastRequest()->getHeaderLine('X-Yandex-Music-Device'));
    }

    public function testOneQueueComesWithItsTracks(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'id' => 'q1',
            'currentIndex' => 0,
            'tracks' => [['id' => 31190260, 'albumId' => 4243617]],
        ]]);

        $queue = $this->client($http)->queue('q1');

        self::assertInstanceOf(Queue::class, $queue);
        self::assertInstanceOf(TrackId::class, $queue->current());
        self::assertSame('https://api.music.yandex.net/queues/q1', (string) $http->lastRequest()->getUri());
    }

    /**
     * The index goes in the query and the flag in the body, and success is
     * reported in a field rather than by answering `ok`.
     */
    public function testUpdatingThePosition(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['status' => 'ok']]);

        self::assertTrue($this->client($http)->queueUpdatePosition('q1', 3));
        self::assertSame(
            'https://api.music.yandex.net/queues/q1/update-position?currentIndex=3',
            (string) $http->lastRequest()->getUri(),
        );
        self::assertSame(['isInteractive' => 'False'], $http->formBodyAt(0));
    }

    public function testAPositionUpdateThatDidNotTake(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['status' => 'error']]);

        self::assertFalse($this->client($http)->queueUpdatePosition('q1', 3));
    }

    /**
     * Creating a queue sends the whole thing as JSON and gets an id back.
     */
    public function testCreatingAQueue(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['id' => 'q2']]);

        $queue = new Queue(
            new Context('playlist', '503646255:1042'),
            [new TrackId(31190260, albumId: 4243617)],
            currentIndex: 0,
            from: 'mobile-playlist',
        );

        self::assertSame('q2', $this->client($http)->queueCreate($queue));

        $request = $http->lastRequest();

        self::assertSame('POST', $request->getMethod());
        self::assertSame('application/json', $request->getHeaderLine('Content-Type'));
        self::assertSame('https://api.music.yandex.net/queues', (string) $request->getUri());

        $body = $http->jsonBodyAt(0);
        $context = $body['context'] ?? null;
        $tracks = $body['tracks'] ?? null;

        self::assertIsArray($context);
        self::assertSame('playlist', $context['type'] ?? null);
        self::assertSame(0, $body['currentIndex'] ?? null);
        self::assertSame('mobile-playlist', $body['from'] ?? null);
        self::assertIsArray($tracks);
        self::assertCount(1, $tracks);
        self::assertArrayNotHasKey('client', $body, 'the back-reference is not part of a queue');
    }

    public function testAQueueThatCameBackWithoutAnId(): void
    {
        $http = (new MockHttpClient())->queue(['result' => []]);

        self::assertNull($this->client($http)->queueCreate(new Queue(id: 'q1')));
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
