<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\Diversity;
use LuckyWins\YandexMusic\Model\Rotor\MoodEnergy;
use LuckyWins\YandexMusic\Model\Rotor\RotorSettings;
use LuckyWins\YandexMusic\Model\Rotor\StationLanguage;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RotorSettings::class)]
final class RotorSettingsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return RotorSettings::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'language' => 'any',
            'diversity' => 'favorite',
            'mood' => 2,
            'energy' => 3,
            'moodEnergy' => 'active',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['language' => 'any'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(RotorSettings::class, $model);
        self::assertSame('any', $model->language);
        self::assertSame('favorite', $model->diversity);
        self::assertSame(2, $model->mood);
        self::assertSame(3, $model->energy);
        self::assertSame('active', $model->moodEnergy);

        self::assertSame(StationLanguage::Any, $model->languageOption());
        self::assertSame(Diversity::Favorite, $model->diversityOption());
        self::assertSame(MoodEnergy::Active, $model->moodEnergyOption());
    }

    /**
     * A value this library has not heard of must not be lost: the typed
     * accessor says "not one of ours", and the raw field still holds it. That
     * is the whole reason these are strings with accessors rather than enums.
     */
    public function testAnUnknownValueIsKeptRatherThanFlattened(): void
    {
        $model = RotorSettings::fromApi([
            'language' => 'klingon',
            'diversity' => 'diverse',
            'moodEnergy' => 'nostalgic',
        ], self::client());

        self::assertInstanceOf(RotorSettings::class, $model);

        self::assertSame('klingon', $model->language);
        self::assertNull($model->languageOption());

        // `diverse` is advertised by the stations and refused by the endpoint,
        // so it is exactly the case this has to survive.
        self::assertSame('diverse', $model->diversity);
        self::assertNull($model->diversityOption());

        self::assertSame('nostalgic', $model->moodEnergy);
        self::assertNull($model->moodEnergyOption());
    }

    public function testMissingSettingsHaveNoOption(): void
    {
        $model = RotorSettings::fromApi(['mood' => 1], self::client());

        self::assertInstanceOf(RotorSettings::class, $model);
        self::assertNull($model->languageOption());
        self::assertNull($model->diversityOption());
        self::assertNull($model->moodEnergyOption());
    }

    protected function equalityTriple(): array
    {
        return [
            new RotorSettings('any', 'default'),
            new RotorSettings('any', 'default'),
            new RotorSettings('russian', 'default'),
        ];
    }
}
