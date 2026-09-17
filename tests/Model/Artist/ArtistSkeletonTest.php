<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\ArtistSkeleton;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Skeleton\SkeletonBlock;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistSkeleton::class)]
final class ArtistSkeletonTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistSkeleton::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'artist-page',
            'title' => 'Miyagi & Эндшпиль',
            'blocks' => [
                ['id' => 'popular', 'type' => 'tracks', 'data' => ['title' => 'Популярное']],
                ['id' => 'albums', 'type' => 'albums'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'artist-page'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistSkeleton::class, $model);
        self::assertSame('artist-page', $model->id);
        self::assertSame('Miyagi & Эндшпиль', $model->title);
        self::assertCount(2, $model->blocks);
        self::assertInstanceOf(SkeletonBlock::class, $model->blocks[0]);
        self::assertSame('Популярное', $model->blocks[0]->data?->title);
    }

    protected function equalityTriple(): array
    {
        return [
            new ArtistSkeleton('artist-page'),
            new ArtistSkeleton('artist-page', 'иначе'),
            new ArtistSkeleton('other-page'),
        ];
    }
}
