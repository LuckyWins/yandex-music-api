<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Label;

use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Label\LabelArtists;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LabelArtists::class)]
final class LabelArtistsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return LabelArtists::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'artists' => [['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']],
            'pager' => ['total' => 7, 'page' => 0, 'perPage' => 20],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['artists' => [['id' => 4611844, 'name' => 'Miyagi']]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(LabelArtists::class, $model);
        self::assertCount(1, $model->artists);
        self::assertInstanceOf(Artist::class, $model->artists[0]);
        self::assertSame(7, $model->pager?->total);
    }

    protected function equalityTriple(): array
    {
        return [new LabelArtists([new Artist(1, 'a')]), new LabelArtists([new Artist(1, 'a')]), new LabelArtists([new Artist(2, 'b')])];
    }
}
