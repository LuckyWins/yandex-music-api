<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\MusicHistory;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryItemId;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MusicHistoryItemId::class)]
final class MusicHistoryItemIdTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MusicHistoryItemId::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => '4243617',
            'trackId' => '31190260',
            'albumId' => '4243617',
            'uid' => 503646255,
            'kind' => 1042,
            'seeds' => ['user:onyourwave'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => '4243617'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MusicHistoryItemId::class, $model);
        self::assertSame('4243617', $model->id);
        self::assertSame('31190260', $model->trackId);
        self::assertSame('4243617', $model->albumId);
        self::assertSame(503646255, $model->uid);
        self::assertSame(1042, $model->kind);
        self::assertSame(['user:onyourwave'], $model->seeds);
    }

    protected function equalityTriple(): array
    {
        return [new MusicHistoryItemId('1'), new MusicHistoryItemId('1'), new MusicHistoryItemId('2')];
    }
}
