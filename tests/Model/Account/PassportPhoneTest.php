<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\PassportPhone;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PassportPhone::class)]
final class PassportPhoneTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PassportPhone::class;
    }

    protected static function fullPayload(): array
    {
        return ['phone' => '+79001234567'];
    }

    protected static function requiredPayload(): array
    {
        return ['phone' => '+79001234567'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PassportPhone::class, $model);
        self::assertSame('+79001234567', $model->phone);
    }

    protected function equalityTriple(): array
    {
        return [
            new PassportPhone('+79001234567'),
            new PassportPhone('+79001234567'),
            new PassportPhone('+79007654321'),
        ];
    }
}
