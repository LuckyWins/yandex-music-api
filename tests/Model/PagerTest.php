<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Pager::class)]
final class PagerTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Pager::class;
    }

    protected static function fullPayload(): array
    {
        return ['total' => 88, 'page' => 1, 'perPage' => 20];
    }

    protected static function requiredPayload(): array
    {
        return ['total' => 88, 'page' => 1, 'perPage' => 20];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Pager::class, $model);
        self::assertSame(88, $model->total);
        self::assertSame(1, $model->page);
        self::assertSame(20, $model->perPage);
    }

    protected function equalityTriple(): array
    {
        return [new Pager(88, 1, 20), new Pager(88, 1, 20), new Pager(88, 2, 20)];
    }
}
