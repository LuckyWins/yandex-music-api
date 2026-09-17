<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Clip;

use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Clip\ClipsWillLike;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ClipsWillLike::class)]
final class ClipsWillLikeTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ClipsWillLike::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'clips' => [['clipId' => 91, 'title' => 'Нирвана'], ['clipId' => 92]],
            'pager' => ['total' => 2, 'page' => 0, 'perPage' => 20],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['clips' => [['clipId' => 91]]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ClipsWillLike::class, $model);
        self::assertCount(2, $model->clips);
        self::assertInstanceOf(Clip::class, $model->clips[0]);
        self::assertSame('Нирвана', $model->clips[0]->title);
        self::assertInstanceOf(Pager::class, $model->pager);
        self::assertSame(2, $model->pager->total);
    }

    protected function equalityTriple(): array
    {
        return [
            new ClipsWillLike([new Clip(91)]),
            new ClipsWillLike([new Clip(91)]),
            new ClipsWillLike([new Clip(92)]),
        ];
    }
}
