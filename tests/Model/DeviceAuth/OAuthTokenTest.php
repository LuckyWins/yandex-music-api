<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\DeviceAuth;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\DeviceAuth\OAuthToken;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OAuthToken::class)]
final class OAuthTokenTest extends TestCase
{
    private const ACCESS_TOKEN = 'y0_AgAAAABexampleAAAAAA';
    private const REFRESH_TOKEN = '1:refreshexample';
    private const EXPIRES_IN = 31536000;
    private const TOKEN_TYPE = 'bearer';

    public function testConstructor(): void
    {
        $token = new OAuthToken(self::ACCESS_TOKEN, self::REFRESH_TOKEN, self::EXPIRES_IN, self::TOKEN_TYPE);

        self::assertSame(self::ACCESS_TOKEN, $token->accessToken);
        self::assertSame(self::REFRESH_TOKEN, $token->refreshToken);
        self::assertSame(self::EXPIRES_IN, $token->expiresIn);
        self::assertSame(self::TOKEN_TYPE, $token->tokenType);
    }

    public function testFromApiEmpty(): void
    {
        self::assertNull(OAuthToken::fromApi([], $this->client()));
        self::assertNull(OAuthToken::fromApi(null, $this->client()));
    }

    public function testFromApiRequiredOnly(): void
    {
        $token = OAuthToken::fromApi(['access_token' => self::ACCESS_TOKEN], $this->client());

        self::assertNotNull($token);
        self::assertSame(self::ACCESS_TOKEN, $token->accessToken);
        self::assertNull($token->refreshToken);
        self::assertNull($token->expiresIn);
        self::assertNull($token->tokenType);
    }

    public function testFromApiAll(): void
    {
        $token = OAuthToken::fromApi([
            'access_token' => self::ACCESS_TOKEN,
            'refresh_token' => self::REFRESH_TOKEN,
            'expires_in' => self::EXPIRES_IN,
            'token_type' => self::TOKEN_TYPE,
        ], $this->client());

        self::assertNotNull($token);
        self::assertSame(self::ACCESS_TOKEN, $token->accessToken);
        self::assertSame(self::REFRESH_TOKEN, $token->refreshToken);
        self::assertSame(self::EXPIRES_IN, $token->expiresIn);
        self::assertSame(self::TOKEN_TYPE, $token->tokenType);
    }

    public function testEquality(): void
    {
        $a = new OAuthToken(self::ACCESS_TOKEN);
        $b = new OAuthToken('different');
        $c = new OAuthToken(self::ACCESS_TOKEN, self::REFRESH_TOKEN);

        self::assertFalse($a->equals($b));
        self::assertTrue($a->equals($c));
    }

    public function testToArrayDropsTheClient(): void
    {
        $token = new OAuthToken(self::ACCESS_TOKEN, client: $this->client());

        self::assertSame(
            ['accessToken' => self::ACCESS_TOKEN, 'refreshToken' => null, 'expiresIn' => null, 'tokenType' => null],
            $token->toArray(),
        );
    }

    private function client(): Client
    {
        return new Client(request: new Request(new MockHttpClient()));
    }
}
