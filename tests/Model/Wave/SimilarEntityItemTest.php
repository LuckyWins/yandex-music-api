<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Wave;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Wave\SimilarEntityData;
use LuckyWins\YandexMusic\Model\Wave\SimilarEntityItem;
use LuckyWins\YandexMusic\Model\Wave\Wave;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SimilarEntityItem::class)]
final class SimilarEntityItemTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return SimilarEntityItem::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'type' => 'wave',
            'data' => ['wave' => ['name' => 'Моя волна']],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'wave'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(SimilarEntityItem::class, $model);
        self::assertSame('wave', $model->type);
        self::assertInstanceOf(SimilarEntityData::class, $model->data);
        self::assertSame('Моя волна', $model->data->wave?->name);
    }

    protected function equalityTriple(): array
    {
        return [
            new SimilarEntityItem('wave', new SimilarEntityData(new Wave('Моя волна'))),
            new SimilarEntityItem('wave', new SimilarEntityData(new Wave('Моя волна'))),
            new SimilarEntityItem('wave', new SimilarEntityData(new Wave('Другая волна'))),
        ];
    }
}
