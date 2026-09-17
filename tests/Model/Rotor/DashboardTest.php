<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\Dashboard;
use LuckyWins\YandexMusic\Model\Rotor\Id;
use LuckyWins\YandexMusic\Model\Rotor\Station;
use LuckyWins\YandexMusic\Model\Rotor\StationResult;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Dashboard::class)]
final class DashboardTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Dashboard::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'dashboardId' => '1789647429655083-1998170453172792349',
            'pumpkin' => false,
            'stations' => [
                ['station' => ['id' => ['type' => 'user', 'tag' => 'onyourwave'], 'name' => 'Моя волна']],
                ['station' => ['id' => ['type' => 'genre', 'tag' => 'rap'], 'name' => 'Рэп и хип-хоп']],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['dashboardId' => '1789647429655083-1998170453172792349'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Dashboard::class, $model);
        self::assertSame('1789647429655083-1998170453172792349', $model->dashboardId);
        self::assertFalse($model->pumpkin);
        self::assertCount(2, $model->stations);
        self::assertInstanceOf(StationResult::class, $model->stations[0]);
        self::assertSame('user:onyourwave', $model->stations[0]->station?->id?->tag());
    }

    protected function equalityTriple(): array
    {
        return [
            new Dashboard('d1'),
            new Dashboard('d1', [new StationResult(new Station(new Id('genre', 'rap'), 'Рэп'))]),
            new Dashboard('d2'),
        ];
    }
}
