<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\Value;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Value::class)]
final class ValueTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Value::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'value' => 'favorite',
            'name' => 'Любимое',
            'imageUrl' => 'https://avatars.invalid/img.png',
            'unspecified' => false,
            'serializedSeed' => 'settingDiversity:favorite',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['value' => 'favorite', 'name' => 'Любимое'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Value::class, $model);
        self::assertSame('favorite', $model->value);
        self::assertSame('Любимое', $model->name);
        self::assertSame('https://avatars.invalid/img.png', $model->imageUrl);
        self::assertFalse($model->unspecified);
        self::assertSame('settingDiversity:favorite', $model->serializedSeed);
    }

    /**
     * The older `restrictions` sends two fields; the newer `restrictions2`
     * sends five. Both have to work.
     */
    public function testTheOlderTwoFieldForm(): void
    {
        $model = Value::fromApi(['value' => 'default', 'name' => 'Все'], self::client());

        self::assertInstanceOf(Value::class, $model);
        self::assertNull($model->imageUrl);
        self::assertNull($model->serializedSeed);
    }

    protected function equalityTriple(): array
    {
        return [new Value('default', 'Все'), new Value('default', 'Any'), new Value('popular', 'Все')];
    }
}
