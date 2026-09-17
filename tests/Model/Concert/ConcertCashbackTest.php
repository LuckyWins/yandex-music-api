<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\ConcertCashback;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConcertCashback::class)]
final class ConcertCashbackTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ConcertCashback::class;
    }

    protected static function fullPayload(): array
    {
        return ['title' => 'Кешбэк баллами', 'valuePercent' => 10];
    }

    protected static function requiredPayload(): array
    {
        return ['valuePercent' => 10];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ConcertCashback::class, $model);
        self::assertSame('Кешбэк баллами', $model->title);
        self::assertSame(10, $model->valuePercent);
    }

    protected function equalityTriple(): array
    {
        return [new ConcertCashback('Кешбэк', 10), new ConcertCashback('Кешбэк', 10), new ConcertCashback('Кешбэк', 15)];
    }
}
