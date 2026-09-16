<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\CustomWave;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CustomWave::class)]
final class CustomWaveTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return CustomWave::class;
    }

    protected static function fullPayload(): array
    {
        return ['title' => 'Miyagi & Эндшпиль', 'animationUrl' => 'https://example.invalid/a.json', 'header' => 'Моя волна', 'backgroundImageUrl' => 'https://example.invalid/b.jpg'];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'Miyagi & Эндшпиль'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(CustomWave::class, $model);
        self::assertSame('Miyagi & Эндшпиль', $model->title);
        self::assertSame('Моя волна', $model->header);
    }

    protected function equalityTriple(): array
    {
        return [new CustomWave('t', header: 'h'), new CustomWave('t', header: 'h'), new CustomWave('u', header: 'h')];
    }
}
