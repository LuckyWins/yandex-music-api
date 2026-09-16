<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\Description;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Description::class)]
final class DescriptionTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Description::class;
    }

    protected static function fullPayload(): array
    {
        return ['text' => 'Группа из Сиэтла', 'uri' => 'https://ru.wikipedia.org/wiki/Nirvana'];
    }

    protected static function requiredPayload(): array
    {
        return ['text' => 'Группа из Сиэтла'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Description::class, $model);
        self::assertSame('Группа из Сиэтла', $model->text);
        self::assertStringContainsString('wikipedia', (string) $model->uri);
    }

    protected function equalityTriple(): array
    {
        return [new Description('t', 'u'), new Description('t', 'u'), new Description('t', 'v')];
    }
}
