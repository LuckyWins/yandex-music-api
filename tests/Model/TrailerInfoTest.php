<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\TrailerInfo;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TrailerInfo::class)]
final class TrailerInfoTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TrailerInfo::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'title' => 'О чём этот плейлист',
            'tracks' => [
                ['id' => 31190260, 'title' => 'Нирвана'],
                ['id' => 31190261, 'title' => 'Тёмный рыцарь'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'О чём этот плейлист'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TrailerInfo::class, $model);
        self::assertSame('О чём этот плейлист', $model->title);
        self::assertCount(2, $model->tracks);
        self::assertInstanceOf(Track::class, $model->tracks[0]);
        self::assertSame('Нирвана', $model->tracks[0]->title);
    }

    protected function equalityTriple(): array
    {
        return [
            new TrailerInfo('О чём этот плейлист'),
            new TrailerInfo('О чём этот плейлист'),
            new TrailerInfo('Другой трейлер'),
        ];
    }
}
