<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Wave;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Wave\Wave;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Wave::class)]
final class WaveTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Wave::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'name' => 'Моя волна',
            'description' => 'Музыка под настроение',
            'seeds' => ['user:onyourwave', 'genre:rock'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['name' => 'Моя волна'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Wave::class, $model);
        self::assertSame('Моя волна', $model->name);
        self::assertSame('Музыка под настроение', $model->description);
        self::assertSame(['user:onyourwave', 'genre:rock'], $model->seeds);
    }

    protected function equalityTriple(): array
    {
        return [
            new Wave('Моя волна', seeds: ['a']),
            new Wave('Моя волна', 'другое описание', ['a']),
            new Wave('Волна артиста', seeds: ['a']),
        ];
    }
}
