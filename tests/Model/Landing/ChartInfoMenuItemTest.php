<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\ChartInfoMenuItem;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ChartInfoMenuItem::class)]
final class ChartInfoMenuItemTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ChartInfoMenuItem::class;
    }

    protected static function fullPayload(): array
    {
        return ['title' => 'Россия', 'url' => '/chart/russia', 'selected' => true];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'Россия'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ChartInfoMenuItem::class, $model);
        self::assertSame('Россия', $model->title);
        self::assertSame('/chart/russia', $model->url);
        self::assertTrue($model->selected);
    }

    protected function equalityTriple(): array
    {
        return [new ChartInfoMenuItem('Россия', '/chart/russia'), new ChartInfoMenuItem('Россия', '/chart/russia', true), new ChartInfoMenuItem('Мир', '/chart/world')];
    }
}
