<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Credit;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Credit::class)]
final class CreditTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Credit::class;
    }

    protected static function fullPayload(): array
    {
        return ['title' => 'Продюсер', 'value' => 'Кто-то'];
    }

    /**
     * Every field here is optional, so there is no smaller payload than one
     * arbitrary field — an empty one deserializes to null by design.
     */
    protected static function requiredPayload(): array
    {
        return ['title' => 'Продюсер'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Credit::class, $model);
        self::assertSame('Продюсер', $model->title);
        self::assertSame('Кто-то', $model->value);
    }

    protected function equalityTriple(): array
    {
        return [new Credit('a', 'b'), new Credit('a', 'b'), new Credit('a', 'c')];
    }
}
