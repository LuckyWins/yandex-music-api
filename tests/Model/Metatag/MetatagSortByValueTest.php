<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Metatag;

use LuckyWins\YandexMusic\Model\Metatag\MetatagSortByValue;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MetatagSortByValue::class)]
final class MetatagSortByValueTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MetatagSortByValue::class;
    }

    protected static function fullPayload(): array
    {
        return ['value' => 'popular', 'title' => 'Популярные', 'active' => true];
    }

    protected static function requiredPayload(): array
    {
        return ['value' => 'popular'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MetatagSortByValue::class, $model);
        self::assertSame('popular', $model->value);
        self::assertSame('Популярные', $model->title);
        self::assertTrue($model->active);
    }

    protected function equalityTriple(): array
    {
        return [new MetatagSortByValue('popular'), new MetatagSortByValue('popular', 'Популярные'), new MetatagSortByValue('new')];
    }
}
