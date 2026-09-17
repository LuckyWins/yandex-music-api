<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\AdParams;
use LuckyWins\YandexMusic\Model\Rotor\Id;
use LuckyWins\YandexMusic\Model\Rotor\RotorSettings;
use LuckyWins\YandexMusic\Model\Rotor\Station;
use LuckyWins\YandexMusic\Model\Rotor\StationResult;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StationResult::class)]
final class StationResultTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return StationResult::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'station' => ['id' => ['type' => 'genre', 'tag' => 'allrock'], 'name' => 'Рок'],
            'settings' => ['language' => 'any', 'diversity' => 'default'],
            'settings2' => ['language' => 'any', 'diversity' => 'favorite'],
            'adParams' => ['partnerId' => 1, 'categoryId' => 2, 'pageRef' => 'ref', 'adVolume' => -13],
            'explanation' => 'Потому что вы слушали рок',
            'prerolls' => [['id' => 'ad-1']],
            'rupTitle' => 'Рок',
            'rupDescription' => 'Лучшее из рока',
            'customName' => 'Мой рок',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['station' => ['name' => 'Рок']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(StationResult::class, $model);
        self::assertInstanceOf(Station::class, $model->station);
        self::assertSame('genre:allrock', $model->station->id?->tag());

        // Two settings objects, the second being the newer arrangement.
        self::assertInstanceOf(RotorSettings::class, $model->settings);
        self::assertSame('default', $model->settings->diversity);
        self::assertInstanceOf(RotorSettings::class, $model->settings2);
        self::assertSame('favorite', $model->settings2->diversity);

        self::assertInstanceOf(AdParams::class, $model->adParams);
        self::assertSame(-13, $model->adParams->adVolume);
        self::assertSame('Потому что вы слушали рок', $model->explanation);
        self::assertSame([['id' => 'ad-1']], $model->prerolls);
        self::assertSame('Рок', $model->rupTitle);
        self::assertSame('Лучшее из рока', $model->rupDescription);
        self::assertSame('Мой рок', $model->customName);
    }

    protected function equalityTriple(): array
    {
        return [
            new StationResult(new Station(new Id('genre', 'allrock'), 'Рок')),
            new StationResult(new Station(new Id('genre', 'allrock'), 'Рок'), new RotorSettings('any')),
            new StationResult(new Station(new Id('genre', 'rap'), 'Рэп')),
        ];
    }
}
