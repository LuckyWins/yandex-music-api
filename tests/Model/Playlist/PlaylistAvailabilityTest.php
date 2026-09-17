<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistAvailability;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlaylistAvailability::class)]
final class PlaylistAvailabilityTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PlaylistAvailability::class;
    }

    protected static function fullPayload(): array
    {
        return ['available' => true];
    }

    protected static function requiredPayload(): array
    {
        return ['available' => true];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PlaylistAvailability::class, $model);
        self::assertTrue($model->available);
    }

    protected function equalityTriple(): array
    {
        return [new PlaylistAvailability(true), new PlaylistAvailability(true), new PlaylistAvailability(false)];
    }
}
