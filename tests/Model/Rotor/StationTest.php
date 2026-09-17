<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Icon;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\Id;
use LuckyWins\YandexMusic\Model\Rotor\Restrictions;
use LuckyWins\YandexMusic\Model\Rotor\Station;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Station::class)]
final class StationTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Station::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => ['type' => 'genre', 'tag' => 'allrock'],
            'parentId' => ['type' => 'genre', 'tag' => 'rock'],
            'name' => 'Рок',
            'idForFrom' => 'genre_allrock',
            'fullImageUrl' => 'avatars.invalid/full/%%',
            'mtsFullImageUrl' => 'avatars.invalid/mts/%%',
            'icon' => ['backgroundColor' => '#ff0000', 'imageUrl' => 'avatars.invalid/icon/%%'],
            'mtsIcon' => ['backgroundColor' => '#00ff00', 'imageUrl' => 'avatars.invalid/mts-icon/%%'],
            'geocellIcon' => ['backgroundColor' => '#0000ff', 'imageUrl' => 'avatars.invalid/geo/%%'],
            'restrictions' => ['language' => ['type' => 'enum', 'name' => 'Язык']],
            'restrictions2' => ['diversity' => ['type' => 'enum', 'name' => 'Разнообразие']],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['name' => 'Рок'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Station::class, $model);
        self::assertSame('Рок', $model->name);
        self::assertInstanceOf(Id::class, $model->id);
        self::assertSame('genre:allrock', $model->id->tag());
        self::assertSame('genre:rock', $model->parentId?->tag());
        self::assertSame('genre_allrock', $model->idForFrom);
        self::assertInstanceOf(Icon::class, $model->icon);
        self::assertInstanceOf(Icon::class, $model->mtsIcon);
        self::assertInstanceOf(Icon::class, $model->geocellIcon);
        self::assertSame('avatars.invalid/full/%%', $model->fullImageUrl);
        self::assertSame('avatars.invalid/mts/%%', $model->mtsFullImageUrl);

        // Two arrangements of the same thing, both modelled the same way.
        self::assertInstanceOf(Restrictions::class, $model->restrictions);
        self::assertInstanceOf(Restrictions::class, $model->restrictions2);
        self::assertSame('Разнообразие', $model->restrictions2->diversity?->name);
    }

    protected function equalityTriple(): array
    {
        return [
            new Station(new Id('genre', 'allrock'), 'Рок'),
            new Station(new Id('genre', 'allrock'), 'Рок'),
            new Station(new Id('genre', 'rap'), 'Рэп'),
        ];
    }
}
