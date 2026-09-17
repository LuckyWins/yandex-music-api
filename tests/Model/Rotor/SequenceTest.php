<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\Sequence;
use LuckyWins\YandexMusic\Model\Rotor\TrackParameters;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Sequence::class)]
final class SequenceTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Sequence::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'type' => 'track',
            'liked' => false,
            'track' => ['id' => 31190260, 'title' => 'Нирвана'],
            'trackParameters' => ['bpm' => 128, 'hue' => 210, 'energy' => 0.75],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'track'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Sequence::class, $model);
        self::assertSame('track', $model->type);
        self::assertFalse($model->liked);
        self::assertInstanceOf(Track::class, $model->track);
        self::assertSame('Нирвана', $model->track->title);
        self::assertInstanceOf(TrackParameters::class, $model->trackParameters);
        self::assertSame(128, $model->trackParameters->bpm);
    }

    protected function equalityTriple(): array
    {
        return [
            new Sequence('track', new Track(31190260)),
            new Sequence('track', new Track(31190260), true),
            new Sequence('track', new Track(31190261)),
        ];
    }
}
