<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\AdParams;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AdParams::class)]
final class AdParamsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return AdParams::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'partnerId' => '142700',
            'categoryId' => 1,
            'pageRef' => 'music.yandex.ru',
            'targetRef' => 'music.yandex.ru/radio',
            'otherParams' => 'user:503646255',
            'adVolume' => -13,
            'genreId' => 'allrock',
            'genreName' => 'Рок',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['partnerId' => '142700'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(AdParams::class, $model);
        self::assertSame('142700', $model->partnerId);
        self::assertSame(1, $model->categoryId);
        self::assertSame('music.yandex.ru', $model->pageRef);
        self::assertSame('music.yandex.ru/radio', $model->targetRef);
        self::assertSame('user:503646255', $model->otherParams);
        self::assertSame(-13, $model->adVolume);
        self::assertSame('allrock', $model->genreId);
        self::assertSame('Рок', $model->genreName);
    }

    protected function equalityTriple(): array
    {
        return [
            new AdParams('142700', 1, 'ref'),
            new AdParams('142700', 1, 'ref', 'target'),
            new AdParams('142701', 1, 'ref'),
        ];
    }
}
