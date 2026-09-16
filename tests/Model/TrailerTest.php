<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Trailer;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Trailer::class)]
final class TrailerTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Trailer::class;
    }

    protected static function fullPayload(): array
    {
        return ['available' => true];
    }

    protected static function requiredPayload(): array
    {
        return ['available' => true];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Trailer::class, $model);
        self::assertTrue($model->available);
    }

    protected function equalityTriple(): array
    {
        return [new Trailer(true), new Trailer(true), new Trailer(false)];
    }
}
