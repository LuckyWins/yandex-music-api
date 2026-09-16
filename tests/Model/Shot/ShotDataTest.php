<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Shot;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Shot\ShotData;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ShotData::class)]
final class ShotDataTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ShotData::class;
    }

    protected static function fullPayload(): array
    {
        return ['coverUri' => 'c', 'mdsUrl' => 'https://example.invalid/a.mp3', 'shotText' => 'Привет', 'shotType' => ['id' => 'alice', 'title' => 'Шот']];
    }

    protected static function requiredPayload(): array
    {
        return ['coverUri' => 'c', 'mdsUrl' => 'https://example.invalid/a.mp3', 'shotText' => 'Привет'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ShotData::class, $model);
        self::assertSame('Привет', $model->shotText);
        self::assertSame('alice', $model->shotType?->id);
    }

    protected function equalityTriple(): array
    {
        return [new ShotData('c', 'm', 't'), new ShotData('c', 'm', 't'), new ShotData('c', 'm', 'u')];
    }
}
