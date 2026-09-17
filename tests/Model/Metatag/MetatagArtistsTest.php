<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Metatag;

use LuckyWins\YandexMusic\Model\Metatag\MetatagArtistEntry;
use LuckyWins\YandexMusic\Model\Metatag\MetatagArtists;
use LuckyWins\YandexMusic\Model\Metatag\MetatagSortByValue;
use LuckyWins\YandexMusic\Model\Metatag\MetatagTitle;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MetatagArtists::class)]
final class MetatagArtistsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MetatagArtists::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'run',
            'title' => ['title' => 'Для бега'],
            'coverUri' => 'avatars.invalid/tag/%%',
            'color' => '#ff0000',
            'stationId' => 'tag:run',
            'pager' => ['total' => 120, 'page' => 0, 'perPage' => 20],
            'artists' => [['artist' => ['id' => 4611844, 'name' => 'Miyagi'], 'popularTracks' => [['id' => 31190260, 'title' => 'Нирвана']]]],
            'sortByValues' => [['value' => 'popular', 'title' => 'Популярные', 'active' => true]],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'run'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MetatagArtists::class, $model);
        self::assertSame('run', $model->id);
        self::assertSame('Для бега', $model->title?->title);
        self::assertSame('tag:run', $model->stationId);
        self::assertInstanceOf(Pager::class, $model->pager);
        self::assertSame(120, $model->pager->total);
        self::assertCount(1, $model->artists);
        self::assertInstanceOf(MetatagArtistEntry::class, $model->artists[0]);
        self::assertSame('Miyagi', $model->artists[0]->artist?->name);
        self::assertCount(1, $model->artists[0]->popularTracks, 'a few tracks to hear why');
        self::assertCount(1, $model->sortByValues);
        self::assertInstanceOf(MetatagSortByValue::class, $model->sortByValues[0]);
    }

    protected function equalityTriple(): array
    {
        return [new MetatagArtists('run'), new MetatagArtists('run', new MetatagTitle('Для бега')), new MetatagArtists('sleep')];
    }
}
