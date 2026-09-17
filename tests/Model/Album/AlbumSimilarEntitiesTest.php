<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Album;

use LuckyWins\YandexMusic\Model\Album\AlbumSimilarEntities;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Wave\SimilarEntityItem;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AlbumSimilarEntities::class)]
final class AlbumSimilarEntitiesTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return AlbumSimilarEntities::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'items' => [
                ['type' => 'wave', 'data' => ['wave' => ['name' => 'Моя волна']]],
                ['type' => 'wave'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['items' => [['type' => 'wave']]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(AlbumSimilarEntities::class, $model);
        self::assertCount(2, $model->items);
        self::assertInstanceOf(SimilarEntityItem::class, $model->items[0]);
        self::assertSame('Моя волна', $model->items[0]->data?->wave?->name);
    }

    protected function equalityTriple(): array
    {
        return [new AlbumSimilarEntities([new SimilarEntityItem('wave')]), new AlbumSimilarEntities([new SimilarEntityItem('wave')]), new AlbumSimilarEntities([new SimilarEntityItem('agent')])];
    }
}
