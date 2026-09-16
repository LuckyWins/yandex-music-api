<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\DeviceAuth;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\DeviceAuth\DeviceCode;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DeviceCode::class)]
final class DeviceCodeTest extends TestCase
{
    private const DEVICE_CODE = 'abcdef0123456789';
    private const USER_CODE = 'ABCDEFG';
    private const VERIFICATION_URL = 'https://oauth.yandex.ru/authorize/device';
    private const EXPIRES_IN = 300;
    private const INTERVAL = 5;

    public function testConstructor(): void
    {
        $code = $this->model();

        self::assertSame(self::DEVICE_CODE, $code->deviceCode);
        self::assertSame(self::USER_CODE, $code->userCode);
        self::assertSame(self::VERIFICATION_URL, $code->verificationUrl);
        self::assertSame(self::EXPIRES_IN, $code->expiresIn);
        self::assertSame(self::INTERVAL, $code->interval);
    }

    public function testFromApiEmpty(): void
    {
        self::assertNull(DeviceCode::fromApi([], $this->client()));
        self::assertNull(DeviceCode::fromApi(null, $this->client()));
    }

    /**
     * Every field of this model is required, so there is no reduced payload to
     * test — an incomplete one is an error rather than a partially filled model.
     */
    public function testFromApiMissingRequiredField(): void
    {
        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('missing required fields');

        DeviceCode::fromApi(['device_code' => self::DEVICE_CODE], $this->client());
    }

    public function testFromApiAll(): void
    {
        $code = DeviceCode::fromApi($this->payload(), $this->client());

        self::assertNotNull($code);
        self::assertSame(self::DEVICE_CODE, $code->deviceCode);
        self::assertSame(self::USER_CODE, $code->userCode);
        self::assertSame(self::VERIFICATION_URL, $code->verificationUrl);
        self::assertSame(self::EXPIRES_IN, $code->expiresIn);
        self::assertSame(self::INTERVAL, $code->interval);
    }

    /**
     * The OAuth endpoint answers in snake_case while the properties are
     * camelCase, so every field here exercises the key normalizer.
     */
    public function testUnknownFieldsAreDropped(): void
    {
        $code = DeviceCode::fromApi($this->payload() + ['something_new' => 'value'], $this->client());

        self::assertNotNull($code);
        self::assertSame(self::DEVICE_CODE, $code->deviceCode);
    }

    public function testEquality(): void
    {
        $a = $this->model();
        $b = $this->model(deviceCode: 'other');
        $c = $this->model(expiresIn: 9000);

        self::assertFalse($a->equals($b));
        // Identity is the code pair, so a differing lifetime is still the same code.
        self::assertTrue($a->equals($c));
        self::assertFalse($a->equals(null));
    }

    private function model(string $deviceCode = self::DEVICE_CODE, int $expiresIn = self::EXPIRES_IN): DeviceCode
    {
        return new DeviceCode(
            $deviceCode,
            self::USER_CODE,
            self::VERIFICATION_URL,
            $expiresIn,
            self::INTERVAL,
        );
    }

    /** @return array<string, mixed> */
    private function payload(): array
    {
        return [
            'device_code' => self::DEVICE_CODE,
            'user_code' => self::USER_CODE,
            'verification_url' => self::VERIFICATION_URL,
            'expires_in' => self::EXPIRES_IN,
            'interval' => self::INTERVAL,
        ];
    }

    private function client(): Client
    {
        return new Client(request: new Request(new MockHttpClient()));
    }
}
