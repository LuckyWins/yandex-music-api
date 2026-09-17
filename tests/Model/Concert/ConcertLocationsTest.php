<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\ConcertLocation;
use LuckyWins\YandexMusic\Model\Concert\ConcertLocations;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConcertLocations::class)]
final class ConcertLocationsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ConcertLocations::class;
    }

    protected static function fullPayload(): array
    {
        return ['locations' => [['id' => 213, 'name' => 'Москва'], ['id' => 2, 'name' => 'Санкт-Петербург']]];
    }

    protected static function requiredPayload(): array
    {
        return ['locations' => [['id' => 213]]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ConcertLocations::class, $model);
        self::assertCount(2, $model->locations);
        self::assertInstanceOf(ConcertLocation::class, $model->locations[0]);
        self::assertSame('Санкт-Петербург', $model->locations[1]->name);
    }

    /**
     * The listing is filtered by city id, and a caller has a city name.
     */
    public function testACityIdByName(): void
    {
        $model = ConcertLocations::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(ConcertLocations::class, $model);
        self::assertSame(213, $model->idOf('Москва'));
        self::assertNull($model->idOf('Тбилиси'));
    }

    protected function equalityTriple(): array
    {
        return [new ConcertLocations([new ConcertLocation(213)]), new ConcertLocations([new ConcertLocation(213)]), new ConcertLocations([new ConcertLocation(2)])];
    }
}
