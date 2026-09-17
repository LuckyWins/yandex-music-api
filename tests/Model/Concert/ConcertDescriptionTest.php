<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\ConcertDescription;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConcertDescription::class)]
final class ConcertDescriptionTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ConcertDescription::class;
    }

    protected static function fullPayload(): array
    {
        return ['text' => 'Главный экспериментатор рэп-кухни', 'source' => 'afisha'];
    }

    protected static function requiredPayload(): array
    {
        return ['text' => 'Главный экспериментатор рэп-кухни'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ConcertDescription::class, $model);
        self::assertSame('Главный экспериментатор рэп-кухни', $model->text);
        self::assertSame('afisha', $model->source);
    }

    protected function equalityTriple(): array
    {
        return [
            new ConcertDescription('текст', 'afisha'),
            new ConcertDescription('текст', 'afisha'),
            new ConcertDescription('другой', 'afisha'),
        ];
    }
}
