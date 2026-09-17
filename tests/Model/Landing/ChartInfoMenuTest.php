<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\ChartInfoMenu;
use LuckyWins\YandexMusic\Model\Landing\ChartInfoMenuItem;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ChartInfoMenu::class)]
final class ChartInfoMenuTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ChartInfoMenu::class;
    }

    protected static function fullPayload(): array
    {
        return ['items' => [
            ['title' => 'Россия', 'url' => '/chart/russia'],
            ['title' => 'Мир', 'url' => '/chart/world', 'selected' => true],
        ]];
    }

    protected static function requiredPayload(): array
    {
        return ['items' => [['title' => 'Россия', 'url' => '/chart/russia']]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ChartInfoMenu::class, $model);
        self::assertCount(2, $model->items);
        self::assertInstanceOf(ChartInfoMenuItem::class, $model->items[0]);
        self::assertSame('Мир', $model->selected()?->title);
    }

    public function testNothingIsSelectedUntilSomethingSaysSo(): void
    {
        $model = ChartInfoMenu::fromApi(self::requiredPayload(), self::client());

        self::assertInstanceOf(ChartInfoMenu::class, $model);
        self::assertNull($model->selected());
    }

    protected function equalityTriple(): array
    {
        return [
            new ChartInfoMenu([new ChartInfoMenuItem('Россия', '/chart/russia')]),
            new ChartInfoMenu([new ChartInfoMenuItem('Россия', '/chart/russia')]),
            new ChartInfoMenu([new ChartInfoMenuItem('Мир', '/chart/world')]),
        ];
    }
}
