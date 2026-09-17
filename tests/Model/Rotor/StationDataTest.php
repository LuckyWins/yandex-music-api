<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\StationData;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StationData::class)]
final class StationDataTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return StationData::class;
    }

    protected static function fullPayload(): array
    {
        return ['name' => 'Моя волна'];
    }

    protected static function requiredPayload(): array
    {
        return ['name' => 'Моя волна'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(StationData::class, $model);
        self::assertSame('Моя волна', $model->name);
    }

    protected function equalityTriple(): array
    {
        return [
            new StationData('x'),
            new StationData('x'),
            new StationData('y'),
        ];
    }
}
