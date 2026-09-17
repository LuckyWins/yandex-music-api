<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistAbsence;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlaylistAbsence::class)]
final class PlaylistAbsenceTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PlaylistAbsence::class;
    }

    protected static function fullPayload(): array
    {
        return ['kind' => 1042, 'reason' => 'deleted'];
    }

    protected static function requiredPayload(): array
    {
        return self::fullPayload();
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PlaylistAbsence::class, $model);
        self::assertSame(1042, $model->kind);
        self::assertSame('deleted', $model->reason);
    }

    protected function equalityTriple(): array
    {
        return [
            new PlaylistAbsence(1042, 'deleted'),
            new PlaylistAbsence(1042, 'deleted'),
            new PlaylistAbsence(1042, 'private'),
        ];
    }
}
