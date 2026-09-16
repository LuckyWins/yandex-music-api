<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Major;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Major::class)]
final class MajorTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Major::class;
    }

    protected static function fullPayload(): array
    {
        return ['id' => 1, 'name' => 'Universal Music'];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 1, 'name' => 'Universal Music'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Major::class, $model);
        self::assertSame(1, $model->id);
        self::assertSame('Universal Music', $model->name);
    }

    protected function equalityTriple(): array
    {
        return [new Major(1, 'a'), new Major(1, 'b'), new Major(2, 'a')];
    }
}
