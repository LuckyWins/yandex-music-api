<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistId;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlaylistId::class)]
final class PlaylistIdTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PlaylistId::class;
    }

    protected static function fullPayload(): array
    {
        return ['uid' => 503646255, 'kind' => 1042, 'playlistUuid' => 'a9f0c1e2-0000-4000-8000-000000000001'];
    }

    protected static function requiredPayload(): array
    {
        return ['kind' => 1042];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PlaylistId::class, $model);
        self::assertSame(503646255, $model->uid);
        self::assertSame(1042, $model->kind);
        self::assertSame('503646255:1042', $model->pair());
        self::assertSame('a9f0c1e2-0000-4000-8000-000000000001', $model->playlistUuid);
    }

    public function testThePairNeedsBothHalves(): void
    {
        $model = PlaylistId::fromApi(['kind' => 1042], self::client());

        self::assertInstanceOf(PlaylistId::class, $model);
        self::assertNull($model->pair());
    }

    protected function equalityTriple(): array
    {
        return [new PlaylistId(1, 1042), new PlaylistId(1, 1042), new PlaylistId(1, 1043)];
    }
}
