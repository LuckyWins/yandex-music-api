<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\DiscreteScale;
use LuckyWins\YandexMusic\Model\Rotor\Enum;
use LuckyWins\YandexMusic\Model\Rotor\Restrictions;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Restrictions::class)]
final class RestrictionsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Restrictions::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'language' => ['type' => 'enum', 'name' => 'Язык', 'possibleValues' => [
                ['value' => 'any', 'name' => 'Любой'],
            ]],
            'diversity' => ['type' => 'enum', 'name' => 'Разнообразие', 'possibleValues' => [
                ['value' => 'default', 'name' => 'Все'],
            ]],
            'moodEnergy' => ['type' => 'enum', 'name' => 'Настроение', 'possibleValues' => [
                ['value' => 'all', 'name' => 'Все'],
            ]],
            'mood' => ['type' => 'discrete-scale', 'name' => 'Настроение', 'min' => ['value' => '1', 'name' => 'Грустное']],
            'energy' => ['type' => 'discrete-scale', 'name' => 'Энергичность'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['language' => ['type' => 'enum', 'name' => 'Язык']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Restrictions::class, $model);
        self::assertInstanceOf(Enum::class, $model->language);
        self::assertSame(['any'], $model->language->values());
        self::assertInstanceOf(Enum::class, $model->diversity);
        self::assertInstanceOf(Enum::class, $model->moodEnergy);
        self::assertInstanceOf(DiscreteScale::class, $model->mood);
        self::assertInstanceOf(DiscreteScale::class, $model->energy);
    }

    protected function equalityTriple(): array
    {
        return [
            new Restrictions(new Enum('enum', 'Язык')),
            new Restrictions(new Enum('enum', 'Язык')),
            new Restrictions(new Enum('enum', 'Разнообразие')),
        ];
    }
}
