<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Shot;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Shot\ShotType;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ShotType::class)]
final class ShotTypeTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ShotType::class;
    }

    protected static function fullPayload(): array
    {
        return ['id' => 'alice', 'title' => 'Шот от Алисы'];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'alice', 'title' => 'Шот от Алисы'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ShotType::class, $model);
        self::assertSame('alice', $model->id);
        self::assertSame('Шот от Алисы', $model->title);
    }

    protected function equalityTriple(): array
    {
        return [new ShotType('a', 't'), new ShotType('a', 'u'), new ShotType('b', 't')];
    }
}
