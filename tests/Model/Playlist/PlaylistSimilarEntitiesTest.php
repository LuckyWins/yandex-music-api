<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistSimilarEntities;
use LuckyWins\YandexMusic\Model\Wave\SimilarEntityItem;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlaylistSimilarEntities::class)]
final class PlaylistSimilarEntitiesTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PlaylistSimilarEntities::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'items' => [
                ['type' => 'wave', 'data' => ['wave' => ['name' => 'Моя волна']]],
                ['type' => 'wave', 'data' => ['agent' => ['animationUri' => 'https://a.invalid/1.json']]],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['items' => [['type' => 'wave']]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PlaylistSimilarEntities::class, $model);
        self::assertCount(2, $model->items);
        self::assertInstanceOf(SimilarEntityItem::class, $model->items[0]);
        self::assertSame('Моя волна', $model->items[0]->data?->wave?->name);
        self::assertSame('https://a.invalid/1.json', $model->items[1]->data?->agent?->animationUri);
    }

    protected function equalityTriple(): array
    {
        return [
            new PlaylistSimilarEntities([new SimilarEntityItem('wave')]),
            new PlaylistSimilarEntities([new SimilarEntityItem('wave')]),
            new PlaylistSimilarEntities([new SimilarEntityItem('agent')]),
        ];
    }
}
