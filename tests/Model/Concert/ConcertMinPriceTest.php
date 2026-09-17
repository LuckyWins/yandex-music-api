<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\ConcertMinPrice;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConcertMinPrice::class)]
final class ConcertMinPriceTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ConcertMinPrice::class;
    }

    protected static function fullPayload(): array
    {
        return ['value' => 3500, 'currency' => 'RUB', 'currencySymbol' => '₽'];
    }

    protected static function requiredPayload(): array
    {
        return ['value' => 3500];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ConcertMinPrice::class, $model);
        self::assertSame(3500, $model->value);
        self::assertSame('RUB', $model->currency);
        self::assertSame('₽', $model->currencySymbol);
    }

    protected function equalityTriple(): array
    {
        return [new ConcertMinPrice(3500, 'RUB'), new ConcertMinPrice(3500, 'RUB', '₽'), new ConcertMinPrice(4000, 'RUB')];
    }
}
