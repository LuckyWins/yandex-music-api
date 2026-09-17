<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\TrackParameters;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TrackParameters::class)]
final class TrackParametersTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TrackParameters::class;
    }

    protected static function fullPayload(): array
    {
        return ['bpm' => 128, 'hue' => 210, 'energy' => 0.75];
    }

    protected static function requiredPayload(): array
    {
        return ['bpm' => 128];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TrackParameters::class, $model);
        self::assertSame(128, $model->bpm);
        self::assertSame(210, $model->hue);
        self::assertSame(0.75, $model->energy);
    }

    protected function equalityTriple(): array
    {
        return [
            new TrackParameters(128, 210, 0.75),
            new TrackParameters(128, 210, 0.75),
            new TrackParameters(120, 210, 0.75),
        ];
    }
}
