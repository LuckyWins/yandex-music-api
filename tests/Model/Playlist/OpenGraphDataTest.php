<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\OpenGraphData;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(OpenGraphData::class)]
final class OpenGraphDataTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return OpenGraphData::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'title' => 'Плейлист дня',
            'description' => 'Обновляется каждый день',
            'image' => ['type' => 'pic', 'uri' => 'avatars.invalid/%%'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'Плейлист дня', 'description' => 'Обновляется каждый день'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(OpenGraphData::class, $model);
        self::assertSame('Плейлист дня', $model->title);
        self::assertSame('Обновляется каждый день', $model->description);
        self::assertInstanceOf(Cover::class, $model->image);
    }

    protected function equalityTriple(): array
    {
        return [
            new OpenGraphData('Плейлист дня', 'Каждый день'),
            new OpenGraphData('Плейлист дня', 'Каждый день'),
            new OpenGraphData('Плейлист недели', 'Каждый день'),
        ];
    }
}
