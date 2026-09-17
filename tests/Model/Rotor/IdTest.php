<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\Id;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Id::class)]
final class IdTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Id::class;
    }

    protected static function fullPayload(): array
    {
        return ['type' => 'genre', 'tag' => 'allrock'];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'genre', 'tag' => 'allrock'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Id::class, $model);
        self::assertSame('genre', $model->type);
        self::assertSame('allrock', $model->tag);
        self::assertSame('genre:allrock', $model->tag());
    }

    protected function equalityTriple(): array
    {
        return [new Id('genre', 'allrock'), new Id('genre', 'allrock'), new Id('user', 'onyourwave')];
    }
}
