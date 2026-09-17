<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Http;

use LuckyWins\YandexMusic\Exception\BadRequestException;
use LuckyWins\YandexMusic\Exception\NetworkException;
use LuckyWins\YandexMusic\Exception\NotFoundException;
use LuckyWins\YandexMusic\Exception\UnauthorizedException;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Request::class)]
final class RequestTest extends TestCase
{
    public function testSendsTheDefaultHeaders(): void
    {
        $http = new MockHttpClient();
        $request = new Request($http);
        $http->queue(['result' => []]);

        $request->get('https://api.music.yandex.net/genres');

        $sent = $http->lastRequest();
        self::assertSame('YandexMusicAndroid/24023621', $sent->getHeaderLine('X-Yandex-Music-Client'));
        self::assertSame('Yandex-Music-API', $sent->getHeaderLine('User-Agent'));
    }

    public function testAuthorizationUsesTheOauthScheme(): void
    {
        $http = new MockHttpClient();
        $request = new Request($http);
        $request->setAuthorization('y0_token');
        $http->queue(['result' => []]);

        $request->get('https://api.music.yandex.net/account/status');

        // Yandex rejects Bearer here; the scheme really is OAuth.
        self::assertSame('OAuth y0_token', $http->lastRequest()->getHeaderLine('Authorization'));
    }

    public function testDefaultHeadersAreNotSharedBetweenInstances(): void
    {
        $first = new Request(new MockHttpClient());
        $first->setAuthorization('y0_token');

        $second = new Request(new MockHttpClient());

        self::assertArrayNotHasKey('Authorization', $second->getHeaders());
    }

    public function testGetAppendsQueryParameters(): void
    {
        $http = new MockHttpClient();
        $request = new Request($http);
        $http->queue(['result' => []]);

        $request->get('https://api.music.yandex.net/search', ['text' => 'nirvana', 'page' => 0]);

        self::assertSame('text=nirvana&page=0', $http->lastRequest()->getUri()->getQuery());
    }

    public function testPostSendsAFormEncodedBody(): void
    {
        $http = new MockHttpClient();
        $request = new Request($http);
        $http->queue(['result' => []]);

        $request->post('https://oauth.yandex.ru/token', ['grant_type' => 'device_code', 'code' => 'abc']);

        $sent = $http->lastRequest();
        self::assertSame('application/x-www-form-urlencoded', $sent->getHeaderLine('Content-Type'));
        self::assertSame('grant_type=device_code&code=abc', (string) $sent->getBody());
    }

    public function testRetrieveReturnsRawBytesAndSendsNoApiHeaders(): void
    {
        $http = new MockHttpClient();
        $request = new Request($http);
        $request->setAuthorization('y0_token');
        $http->queue('not json, just bytes');

        $body = $request->retrieve('https://storage.mds.yandex.net/file');

        self::assertSame('not json, just bytes', $body);
        self::assertSame('', $http->lastRequest()->getHeaderLine('Authorization'));
    }

    /**
     * @param class-string<YandexMusicException> $expected
     */
    #[DataProvider('errorStatuses')]
    public function testMapsStatusesToExceptions(int $status, string $expected): void
    {
        $http = new MockHttpClient();
        $request = new Request($http);
        $http->queue(['error' => ['name' => 'some-error', 'message' => 'It broke']], $status);

        $this->expectException($expected);

        $request->get('https://api.music.yandex.net/genres');
    }

    /** @return iterable<string, array{int, class-string<YandexMusicException>}> */
    public static function errorStatuses(): iterable
    {
        yield '400' => [400, BadRequestException::class];
        yield '401' => [401, UnauthorizedException::class];
        yield '403' => [403, UnauthorizedException::class];
        yield '404' => [404, NotFoundException::class];
        yield '409' => [409, NetworkException::class];
        yield '413' => [413, NetworkException::class];
        yield '502' => [502, NetworkException::class];
        yield '500 falls through to the default' => [500, NetworkException::class];
    }

    public function testCarriesTheErrorCodeOnTheException(): void
    {
        $http = new MockHttpClient();
        $request = new Request($http);
        $http->queue(['error' => 'authorization_pending', 'error_description' => 'Not confirmed'], 400);

        try {
            $request->post('https://oauth.yandex.ru/token');
            self::fail('Expected a BadRequestException');
        } catch (BadRequestException $e) {
            // The code is what callers branch on; the message is free-form text.
            self::assertSame('authorization_pending', $e->getErrorCode());
        }
    }

    public function testUndecodableBodyRaisesALibraryException(): void
    {
        $http = new MockHttpClient();
        $request = new Request($http);
        $http->queue('<html>not json</html>');

        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('Invalid server response');

        $request->get('https://api.music.yandex.net/genres');
    }


    /**
     * Pinning wants a PUT with a JSON body, and unpinning a DELETE with one —
     * a verb that does not usually carry a body at all.
     */
    public function testJsonBodiesOnPutAndDelete(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => 'ok'])
            ->queue(['result' => 'ok']);

        $request = new Request($http);

        $request->putJson('https://api.music.yandex.net/pin/album', ['id' => 4243617]);
        $request->deleteJson('https://api.music.yandex.net/pin/album', ['id' => 4243617]);

        foreach ([0 => 'PUT', 1 => 'DELETE'] as $index => $method) {
            self::assertSame($method, $http->requestAt($index)->getMethod());
            self::assertSame('application/json', $http->requestAt($index)->getHeaderLine('Content-Type'));
            self::assertSame(['id' => 4243617], $http->jsonBodyAt($index));
        }
    }

    /**
     * A header given to one call belongs to that call. The reference library
     * writes its device descriptor into the shared headers, where it then
     * travels with every request made afterwards.
     */
    public function testAPerRequestHeaderDoesNotLinger(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => []])
            ->queue(['result' => []])
            ->queue(['result' => []]);

        $request = new Request($http);

        $request->get('https://api.music.yandex.net/queues', headers: ['X-Yandex-Music-Device' => 'os=iOS']);
        $request->get('https://api.music.yandex.net/genres');
        $request->postJson('https://api.music.yandex.net/queues', ['id' => 'q1'], ['X-Yandex-Music-Device' => 'os=iOS']);

        self::assertSame('os=iOS', $http->requestAt(0)->getHeaderLine('X-Yandex-Music-Device'));
        self::assertSame('', $http->requestAt(1)->getHeaderLine('X-Yandex-Music-Device'));
        self::assertSame('os=iOS', $http->requestAt(2)->getHeaderLine('X-Yandex-Music-Device'));

        // The library's own headers are still there beside it.
        self::assertNotSame('', $http->requestAt(0)->getHeaderLine('X-Yandex-Music-Client'));
    }

    /**
     * An empty or unreadable body leaves the status as the only thing worth
     * saying — and saying it matters: an artist who takes no donations
     * answers 404 with nothing in it, which read as "Unknown HTTP error"
     * until the status was included.
     */
    public function testUndecodableErrorBodyStillMapsTheStatus(): void
    {
        $http = new MockHttpClient();
        $request = new Request($http);
        $http->queue('<html>Gateway Timeout</html>', 404);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Unknown HTTP error (404)');

        $request->get('https://api.music.yandex.net/genres');
    }

    public function testAnEmptyErrorBodyNamesTheStatusToo(): void
    {
        $http = new MockHttpClient();
        $request = new Request($http);
        $http->queue('', 404);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('(404)');

        $request->get('https://api.music.yandex.net/artists/1/blocks/artist-donation');
    }
}
