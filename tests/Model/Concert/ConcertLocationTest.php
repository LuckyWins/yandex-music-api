<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\ConcertLocation;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConcertLocation::class)]
final class ConcertLocationTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ConcertLocation::class;
    }

    protected static function fullPayload(): array
    {
        return ['id' => 213, 'name' => 'Москва'];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 213];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ConcertLocation::class, $model);
        self::assertSame(213, $model->id);
        self::assertSame('Москва', $model->name);
    }

    protected function equalityTriple(): array
    {
        return [new ConcertLocation(213), new ConcertLocation(213, 'Москва'), new ConcertLocation(2)];
    }
}
