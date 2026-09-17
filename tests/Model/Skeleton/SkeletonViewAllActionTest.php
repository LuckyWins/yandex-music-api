<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Skeleton;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Skeleton\SkeletonViewAllAction;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SkeletonViewAllAction::class)]
final class SkeletonViewAllActionTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return SkeletonViewAllAction::class;
    }

    protected static function fullPayload(): array
    {
        return ['deeplink' => 'yandexmusic://artist/1/albums', 'weblink' => 'https://music.invalid/artist/1'];
    }

    protected static function requiredPayload(): array
    {
        return ['weblink' => 'https://music.invalid/artist/1'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(SkeletonViewAllAction::class, $model);
        self::assertSame('yandexmusic://artist/1/albums', $model->deeplink);
        self::assertSame('https://music.invalid/artist/1', $model->weblink);
    }

    protected function equalityTriple(): array
    {
        return [new SkeletonViewAllAction('a'), new SkeletonViewAllAction('a'), new SkeletonViewAllAction('b')];
    }
}
