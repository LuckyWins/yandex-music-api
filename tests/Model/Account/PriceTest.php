<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\Price;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Price::class)]
final class PriceTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Price::class;
    }

    protected static function fullPayload(): array
    {
        return ['amount' => 1690, 'currency' => 'RUB'];
    }

    protected static function requiredPayload(): array
    {
        return ['amount' => 1690, 'currency' => 'RUB'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Price::class, $model);
        self::assertSame(1690, $model->amount);
        self::assertSame('RUB', $model->currency);
    }

    protected function equalityTriple(): array
    {
        return [
            new Price(1690, 'RUB'),
            new Price(1690, 'RUB'),
            new Price(1690, 'USD'),
        ];
    }
}
