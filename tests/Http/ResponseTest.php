<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Http;

use LuckyWins\YandexMusic\Http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Response::class)]
final class ResponseTest extends TestCase
{
    public function testUnwrapsTheResultKey(): void
    {
        $response = Response::fromDecoded([
            'invocationInfo' => ['hostname' => 'host', 'req-id' => 'abc'],
            'result' => ['uid' => 42],
        ]);

        self::assertSame(['uid' => 42], $response->result);
        self::assertFalse($response->hasError());
    }

    /**
     * The OAuth endpoints answer with the payload at the root, so an absent
     * result key means the whole body is the result.
     */
    public function testFallsBackToTheWholeBody(): void
    {
        $response = Response::fromDecoded(['access_token' => 'y0_token', 'expires_in' => 31536000]);

        self::assertSame(['access_token' => 'y0_token', 'expires_in' => 31536000], $response->result);
    }

    public function testKeepsANullResultThatWasSentExplicitly(): void
    {
        $response = Response::fromDecoded(['result' => null]);

        self::assertNull($response->result);
    }

    public function testReadsTheOauthErrorShape(): void
    {
        $response = Response::fromDecoded([
            'error' => 'authorization_pending',
            'error_description' => 'User code not confirmed',
        ]);

        self::assertTrue($response->hasError());
        self::assertSame('authorization_pending', $response->errorCode);
        self::assertSame('User code not confirmed', $response->errorDescription);
        self::assertSame('authorization_pending: User code not confirmed', $response->errorMessage());
    }

    public function testReadsTheApiErrorShape(): void
    {
        $response = Response::fromDecoded([
            'error' => ['name' => 'not-found', 'message' => 'Playlist not found'],
        ]);

        self::assertSame('not-found', $response->errorCode);
        self::assertSame('Playlist not found', $response->errorDescription);
    }

    public function testAcceptsTheCamelCaseSpellingOfTheDescription(): void
    {
        $response = Response::fromDecoded(['error' => 'bad', 'errorDescription' => 'Something broke']);

        self::assertSame('Something broke', $response->errorDescription);
    }

    public function testErrorWithoutADescription(): void
    {
        $response = Response::fromDecoded(['error' => 'expired_token']);

        self::assertSame('expired_token', $response->errorMessage());
    }

    public function testNonArrayBody(): void
    {
        $response = Response::fromDecoded('plain string');

        self::assertSame('plain string', $response->result);
        self::assertFalse($response->hasError());
        self::assertSame('Unknown error', $response->errorMessage());
    }
}
