<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\RenewableRemainder;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RenewableRemainder::class)]
final class RenewableRemainderTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return RenewableRemainder::class;
    }

    protected static function fullPayload(): array
    {
        return ['days' => 12];
    }

    protected static function requiredPayload(): array
    {
        return ['days' => 12];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(RenewableRemainder::class, $model);
        self::assertSame(12, $model->days);
    }

    protected function equalityTriple(): array
    {
        return [
            new RenewableRemainder(12),
            new RenewableRemainder(12),
            new RenewableRemainder(3),
        ];
    }
}
