<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Skeleton;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Skeleton\SkeletonSource;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SkeletonSource::class)]
final class SkeletonSourceTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return SkeletonSource::class;
    }

    protected static function fullPayload(): array
    {
        return ['uri' => '/artists/1/direct-albums', 'count' => 12, 'countWeb' => 10];
    }

    protected static function requiredPayload(): array
    {
        return ['uri' => '/artists/1/direct-albums'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(SkeletonSource::class, $model);
        self::assertSame('/artists/1/direct-albums', $model->uri);
        self::assertSame(12, $model->count);
        self::assertSame(10, $model->countWeb);
    }

    protected function equalityTriple(): array
    {
        return [new SkeletonSource('/a'), new SkeletonSource('/a', 1), new SkeletonSource('/b')];
    }
}
