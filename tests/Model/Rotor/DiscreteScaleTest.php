<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\DiscreteScale;
use LuckyWins\YandexMusic\Model\Rotor\Value;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DiscreteScale::class)]
final class DiscreteScaleTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return DiscreteScale::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'type' => 'discrete-scale',
            'name' => 'Настроение',
            'min' => ['value' => '1', 'name' => 'Грустное'],
            'max' => ['value' => '4', 'name' => 'Весёлое'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'discrete-scale', 'name' => 'Настроение'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(DiscreteScale::class, $model);
        self::assertSame('discrete-scale', $model->type);
        self::assertInstanceOf(Value::class, $model->min);
        self::assertSame('1', $model->min->value);
        self::assertSame('Весёлое', $model->max?->name);
    }

    protected function equalityTriple(): array
    {
        return [
            new DiscreteScale('discrete-scale', 'Настроение'),
            new DiscreteScale('discrete-scale', 'Настроение', new Value('1', 'Грустное')),
            new DiscreteScale('discrete-scale', 'Энергичность'),
        ];
    }
}
