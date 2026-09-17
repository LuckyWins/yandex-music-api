<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Genre;

use LuckyWins\YandexMusic\Model\Genre\Title;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Title::class)]
final class TitleTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Title::class;
    }

    protected static function fullPayload(): array
    {
        return ['title' => 'Рок', 'fullTitle' => 'Рок-музыка'];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'Рок'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Title::class, $model);
        self::assertSame('Рок', $model->title);
        self::assertSame('Рок-музыка', $model->fullTitle);
    }

    protected function equalityTriple(): array
    {
        return [new Title('Рок'), new Title('Рок'), new Title('Рэп')];
    }
}
