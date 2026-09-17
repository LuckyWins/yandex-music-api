<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\Enum;
use LuckyWins\YandexMusic\Model\Rotor\Value;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Enum::class)]
final class EnumTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Enum::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'type' => 'enum',
            'name' => 'Разнообразие',
            'possibleValues' => [
                ['value' => 'default', 'name' => 'Все'],
                ['value' => 'popular', 'name' => 'Популярное'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'enum', 'name' => 'Разнообразие'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Enum::class, $model);
        self::assertSame('enum', $model->type);
        self::assertSame('Разнообразие', $model->name);
        self::assertCount(2, $model->possibleValues);
        self::assertInstanceOf(Value::class, $model->possibleValues[0]);
        self::assertSame(['default', 'popular'], $model->values());
    }

    protected function equalityTriple(): array
    {
        return [
            new Enum('enum', 'Разнообразие'),
            new Enum('enum', 'Разнообразие', [new Value('default', 'Все')]),
            new Enum('enum', 'Язык'),
        ];
    }
}
