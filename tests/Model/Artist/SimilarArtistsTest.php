<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Artist\SimilarArtists;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SimilarArtists::class)]
final class SimilarArtistsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return SimilarArtists::class;
    }

    protected static function fullPayload(): array
    {
        return ['artist' => ['id' => 1, 'name' => 'a'], 'similarArtists' => [['id' => 2, 'name' => 'b'], ['id' => 3, 'name' => 'c']]];
    }

    protected static function requiredPayload(): array
    {
        return ['artist' => ['id' => 1, 'name' => 'a']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(SimilarArtists::class, $model);
        self::assertSame('a', $model->artist?->name);
        self::assertCount(2, $model->similarArtists);
        self::assertSame('c', $model->similarArtists[1]->name);
    }

    protected function equalityTriple(): array
    {
        return [new SimilarArtists(), new SimilarArtists(), new SimilarArtists(new Artist(1))];
    }
}
