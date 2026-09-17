<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Wave;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Wave\SimilarEntityData;
use LuckyWins\YandexMusic\Model\Wave\Wave;
use LuckyWins\YandexMusic\Model\Wave\WaveAgent;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SimilarEntityData::class)]
final class SimilarEntityDataTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return SimilarEntityData::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'wave' => ['name' => 'Моя волна', 'seeds' => ['genre:rock']],
            'agent' => ['animationUri' => 'https://avatars.invalid/animation.json'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['wave' => ['name' => 'Моя волна']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(SimilarEntityData::class, $model);
        self::assertInstanceOf(Wave::class, $model->wave);
        self::assertSame('Моя волна', $model->wave->name);
        self::assertInstanceOf(WaveAgent::class, $model->agent);
    }

    protected function equalityTriple(): array
    {
        return [
            new SimilarEntityData(new Wave('Моя волна')),
            new SimilarEntityData(new Wave('Моя волна')),
            new SimilarEntityData(new Wave('Волна артиста')),
        ];
    }
}
