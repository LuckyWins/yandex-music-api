<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Landing\TrackShortOld;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TrackShortOld::class)]
final class TrackShortOldTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TrackShortOld::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'trackId' => ['id' => 31190260, 'albumId' => 4243617],
            'timestamp' => '2026-09-17T12:00:00+00:00',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['timestamp' => '2026-09-17T12:00:00+00:00'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TrackShortOld::class, $model);
        self::assertInstanceOf(TrackId::class, $model->trackId);
        self::assertSame(31190260, $model->trackId->id);
        self::assertSame(4243617, $model->trackId->albumId);
        self::assertSame('2026-09-17T12:00:00+00:00', $model->timestamp);
    }

    protected function equalityTriple(): array
    {
        return [
            new TrackShortOld(new TrackId(1), 't'),
            new TrackShortOld(new TrackId(1), 't'),
            new TrackShortOld(new TrackId(2), 't'),
        ];
    }
}
