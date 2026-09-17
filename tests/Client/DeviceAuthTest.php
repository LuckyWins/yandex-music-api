<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\DeviceAuth;
use LuckyWins\YandexMusic\Exception\DeviceAuthException;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\DeviceAuth\DeviceCode;
use LuckyWins\YandexMusic\Tests\Support\FrozenClock;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeviceAuth::class)]
final class DeviceAuthTest extends TestCase
{
    private const CLIENT_ID = '23cabbbdc6cd418abb4b39c32c41195d';
    private const CLIENT_SECRET = '53bc75238f0c4d08a118e51fe9203300';

    public function testRequestDeviceCodeReturnsAModel(): void
    {
        $http = (new MockHttpClient())->queue($this->deviceCodeBody());
        $client = $this->client($http);

        $code = $client->requestDeviceCode();

        self::assertSame('dev123', $code->deviceCode);
        self::assertSame('USER01', $code->userCode);
        self::assertSame('https://oauth.yandex.ru/authorize/device', $code->verificationUrl);
        self::assertSame(300, $code->expiresIn);
        self::assertSame(5, $code->interval);
    }

    public function testRequestDeviceCodeSendsTheExpectedPayload(): void
    {
        $http = (new MockHttpClient())->queue($this->deviceCodeBody());
        $client = $this->client($http);

        $client->requestDeviceCode(deviceId: 'my-id', deviceName: 'my-name', clientId: 'my-cid');

        self::assertSame('https://oauth.yandex.ru/device/code', (string) $http->lastRequest()->getUri());
        self::assertSame([
            'client_id' => 'my-cid',
            'device_id' => 'my-id',
            'device_name' => 'my-name',
        ], $http->formBodyAt(0));
    }

    public function testRequestDeviceCodeDefaultsToTheAndroidAppCredentials(): void
    {
        $http = (new MockHttpClient())->queue($this->deviceCodeBody());

        $this->client($http)->requestDeviceCode();

        $body = $http->formBodyAt(0);
        self::assertSame(self::CLIENT_ID, $body['client_id']);
        self::assertSame('YandexMusicAPI', $body['device_name']);
        self::assertMatchesRegularExpression('/^[A-Za-z0-9]{10}$/', $body['device_id']);
    }

    public function testPollDeviceTokenReturnsTheToken(): void
    {
        $http = (new MockHttpClient())->queue([
            'access_token' => 'y0_tok',
            'refresh_token' => '1:ref',
            'expires_in' => 31536000,
            'token_type' => 'bearer',
        ]);

        $token = $this->client($http)->pollDeviceToken('dev123');

        self::assertNotNull($token);
        self::assertSame('y0_tok', $token->accessToken);
        self::assertSame('1:ref', $token->refreshToken);
    }

    public function testPollDeviceTokenSendsTheExpectedPayload(): void
    {
        $http = (new MockHttpClient())->queue(['access_token' => 'y0_tok']);

        $this->client($http)->pollDeviceToken('dev123', 'cid', 'secret');

        self::assertSame('https://oauth.yandex.ru/token', (string) $http->lastRequest()->getUri());
        self::assertSame([
            'grant_type' => 'device_code',
            // The OAuth parameter is `code`, not `device_code`.
            'code' => 'dev123',
            'client_id' => 'cid',
            'client_secret' => 'secret',
        ], $http->formBodyAt(0));
    }

    public function testPollDeviceTokenDefaultsToTheAndroidAppCredentials(): void
    {
        $http = (new MockHttpClient())->queue(['access_token' => 'y0_tok']);

        $this->client($http)->pollDeviceToken('dev123');

        $body = $http->formBodyAt(0);
        self::assertSame(self::CLIENT_ID, $body['client_id']);
        self::assertSame(self::CLIENT_SECRET, $body['client_secret']);
    }

    /**
     * Waiting for the user is the normal state for most of the flow, not an error.
     */
    public function testPollDeviceTokenReturnsNullWhileWaiting(): void
    {
        $http = (new MockHttpClient())->queue(
            ['error' => 'authorization_pending', 'error_description' => 'User code not confirmed'],
            400,
        );

        self::assertNull($this->client($http)->pollDeviceToken('dev123'));
    }

    public function testPollDeviceTokenRaisesOnARealFailure(): void
    {
        $http = (new MockHttpClient())->queue(
            ['error' => 'expired_token', 'error_description' => 'Device code expired'],
            400,
        );

        $this->expectException(DeviceAuthException::class);
        $this->expectExceptionMessage('expired_token');

        $this->client($http)->pollDeviceToken('dev123');
    }

    /**
     * The reference library lets a 401 escape as an UnauthorizedError, which
     * callers of the device flow have no reason to expect.
     */
    public function testPollDeviceTokenTranslatesAnUnauthorizedResponse(): void
    {
        $http = (new MockHttpClient())->queue(['error' => 'invalid_client'], 401);

        $this->expectException(DeviceAuthException::class);

        $this->client($http)->pollDeviceToken('dev123');
    }

    public function testDeviceAuthHappyPath(): void
    {
        $http = (new MockHttpClient())
            ->queue($this->deviceCodeBody())
            ->queue(['error' => 'authorization_pending'], 400)
            ->queue(['error' => 'authorization_pending'], 400)
            ->queue(['access_token' => 'y0_tok', 'token_type' => 'bearer']);

        $clock = new FrozenClock();
        $client = $this->client($http, $clock);

        $shown = null;
        $token = $client->deviceAuth(static function (DeviceCode $code) use (&$shown): void {
            $shown = $code;
        });

        self::assertSame('y0_tok', $token->accessToken);
        self::assertInstanceOf(DeviceCode::class, $shown);
        self::assertSame('USER01', $shown->userCode);

        // One request for the code, three polls.
        self::assertSame(4, $http->requestCount());
        self::assertSame([5.0, 5.0], $clock->sleeps());

        // The token is applied to the client, so later calls are authorized.
        self::assertSame('y0_tok', $client->getToken());
        self::assertSame('OAuth y0_tok', $client->request->getHeaders()['Authorization']);
    }

    public function testDeviceAuthTimesOut(): void
    {
        $http = (new MockHttpClient())
            ->queue($this->deviceCodeBody(expiresIn: 10))
            ->queue(['error' => 'authorization_pending'], 400);

        $client = $this->client($http, new FrozenClock());

        $this->expectException(DeviceAuthException::class);
        $this->expectExceptionMessage('Timed out');

        $client->deviceAuth(static fn (DeviceCode $code) => null);
    }

    public function testDeviceAuthCanBeCancelled(): void
    {
        $http = (new MockHttpClient())
            ->queue($this->deviceCodeBody())
            ->queue(['error' => 'authorization_pending'], 400);

        $client = $this->client($http, new FrozenClock());

        $calls = 0;
        $shouldCancel = static function () use (&$calls): bool {
            ++$calls;

            return $calls >= 3;
        };

        $this->expectException(DeviceAuthException::class);
        $this->expectExceptionMessage('cancelled');

        $client->deviceAuth(static fn (DeviceCode $code) => null, shouldCancel: $shouldCancel);
    }

    /**
     * RFC 8628 says a slow_down means back off by five seconds rather than give
     * up, which is what the reference library does instead.
     */
    public function testDeviceAuthBacksOffOnSlowDown(): void
    {
        $http = (new MockHttpClient())
            ->queue($this->deviceCodeBody())
            ->queue(['error' => 'slow_down'], 400)
            ->queue(['access_token' => 'y0_tok']);

        $clock = new FrozenClock();

        $this->client($http, $clock)->deviceAuth(static fn (DeviceCode $code) => null);

        self::assertSame([10.0], $clock->sleeps());
    }

    public function testDeviceAuthHonoursAnOverriddenInterval(): void
    {
        $http = (new MockHttpClient())
            ->queue($this->deviceCodeBody())
            ->queue(['error' => 'authorization_pending'], 400)
            ->queue(['access_token' => 'y0_tok']);

        $clock = new FrozenClock();

        $this->client($http, $clock)->deviceAuth(static fn (DeviceCode $code) => null, pollInterval: 1.5);

        self::assertSame([1.5], $clock->sleeps());
    }

    public function testRevokeTokenSendsTheExpectedPayload(): void
    {
        $http = (new MockHttpClient())->queue(['status' => 'ok']);

        $this->client($http)->revokeToken('y0_leaked');

        self::assertSame('https://oauth.yandex.ru/revoke_token', (string) $http->lastRequest()->getUri());
        self::assertSame([
            'access_token' => 'y0_leaked',
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
        ], $http->formBodyAt(0));
    }

    public function testRevokeTokenDefaultsToTheClientsOwnToken(): void
    {
        $http = (new MockHttpClient())->queue(['status' => 'ok']);
        $client = new Client('y0_mine', new Request($http), clock: new FrozenClock());

        $client->revokeToken();

        self::assertSame('y0_mine', $http->formBodyAt(0)['access_token']);
    }

    /**
     * Keeping a revoked token around only produces confusing failures later.
     */
    public function testRevokingItsOwnTokenLeavesTheClientUnauthorized(): void
    {
        $http = (new MockHttpClient())->queue(['status' => 'ok']);
        $client = new Client('y0_mine', new Request($http), clock: new FrozenClock());

        $client->revokeToken();

        self::assertNull($client->getToken());
        self::assertArrayNotHasKey('Authorization', $client->request->getHeaders());
    }

    /**
     * Revoking someone else's token says nothing about this client's own.
     */
    public function testRevokingAnotherTokenLeavesThisClientAlone(): void
    {
        $http = (new MockHttpClient())->queue(['status' => 'ok']);
        $client = new Client('y0_mine', new Request($http), clock: new FrozenClock());

        $client->revokeToken('y0_somebody_elses');

        self::assertSame('y0_mine', $client->getToken());
    }

    public function testRevokeTokenNeedsATokenToRevoke(): void
    {
        $http = (new MockHttpClient())->queue(['status' => 'ok']);

        $this->expectException(DeviceAuthException::class);
        $this->expectExceptionMessage('holds no token');

        $this->client($http)->revokeToken();
    }

    /** @return array<string, mixed> */
    private function deviceCodeBody(int $expiresIn = 300): array
    {
        return [
            'device_code' => 'dev123',
            'user_code' => 'USER01',
            'verification_url' => 'https://oauth.yandex.ru/authorize/device',
            'expires_in' => $expiresIn,
            'interval' => 5,
        ];
    }

    private function client(MockHttpClient $http, ?FrozenClock $clock = null): Client
    {
        return new Client(request: new Request($http), clock: $clock ?? new FrozenClock());
    }
}
