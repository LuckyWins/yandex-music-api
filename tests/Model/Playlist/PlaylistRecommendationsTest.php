<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistRecommendations;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlaylistRecommendations::class)]
final class PlaylistRecommendationsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PlaylistRecommendations::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'batchId' => '1559390400000000',
            'tracks' => [
                ['id' => 31190260, 'title' => 'Нирвана'],
                ['id' => 31190261, 'title' => 'Тёмный рыцарь'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['batchId' => '1559390400000000'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PlaylistRecommendations::class, $model);
        self::assertSame('1559390400000000', $model->batchId);
        self::assertCount(2, $model->tracks);
        self::assertInstanceOf(Track::class, $model->tracks[0]);
    }

    protected function equalityTriple(): array
    {
        return [
            new PlaylistRecommendations([], '1'),
            new PlaylistRecommendations([], '1'),
            new PlaylistRecommendations([], '2'),
        ];
    }
}
