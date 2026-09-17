<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\ConcertSkeleton;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Skeleton\SkeletonBlock;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConcertSkeleton::class)]
final class ConcertSkeletonTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ConcertSkeleton::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'concert_page',
            'title' => 'Boulevard Depo',
            'blocks' => [[
                'id' => 'tabs',
                'type' => 'TABS',
                'data' => ['title' => 'О концерте', 'source' => ['uri' => '/concerts/c1/info']],
            ]],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'concert_page'];
    }

    /**
     * The same five models an artist's page uses, written a stage earlier and
     * reused here without a change.
     */
    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ConcertSkeleton::class, $model);
        self::assertSame('concert_page', $model->id);
        self::assertSame('Boulevard Depo', $model->title);
        self::assertCount(1, $model->blocks);
        self::assertInstanceOf(SkeletonBlock::class, $model->blocks[0]);
        self::assertSame('TABS', $model->blocks[0]->type);
        self::assertSame('/concerts/c1/info', $model->blocks[0]->data?->source?->uri);
    }

    protected function equalityTriple(): array
    {
        return [
            new ConcertSkeleton('concert_page'),
            new ConcertSkeleton('concert_page', 'иначе'),
            new ConcertSkeleton('other_page'),
        ];
    }
}
