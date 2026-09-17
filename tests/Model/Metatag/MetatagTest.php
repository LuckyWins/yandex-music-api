<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Metatag;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Metatag\Metatag;
use LuckyWins\YandexMusic\Model\Metatag\MetatagSortByValue;
use LuckyWins\YandexMusic\Model\Metatag\MetatagTitle;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Metatag::class)]
final class MetatagTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Metatag::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'run',
            'title' => ['title' => 'Для бега', 'fullTitle' => 'Музыка для бега'],
            'coverUri' => 'avatars.invalid/tag/%%',
            'color' => '#ff0000',
            'liked' => false,
            'stationId' => 'tag:run',
            'customWaveAnimationUrl' => 'https://avatars.invalid/wave.json',
            'artists' => [['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']],
            'albums' => [['id' => 4243617, 'title' => 'Hajime']],
            'playlists' => [['uid' => 1, 'kind' => 1042]],
            'tracksSortByValues' => [['value' => 'popular', 'title' => 'Популярные', 'active' => true]],
            'albumsSortByValues' => [['value' => 'new', 'title' => 'Новые']],
            'playlistsSortByValues' => [['value' => 'popular']],
            // Sent, and empty on everything seen so far.
            'tracks' => [],
            'composers' => [],
            'promotions' => [],
            'features' => [],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'run'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Metatag::class, $model);
        self::assertSame('run', $model->id);
        self::assertInstanceOf(MetatagTitle::class, $model->title);
        self::assertSame('Музыка для бега', $model->title->fullTitle);
        self::assertSame('avatars.invalid/tag/%%', $model->coverUri);
        self::assertSame('#ff0000', $model->color);
        self::assertFalse($model->liked);
        self::assertSame('tag:run', $model->stationId);
        self::assertSame('https://avatars.invalid/wave.json', $model->customWaveAnimationUrl);

        self::assertInstanceOf(Artist::class, $model->artists[0]);
        self::assertInstanceOf(Album::class, $model->albums[0]);
        self::assertInstanceOf(Playlist::class, $model->playlists[0]);

        // The service advertises how its contents can be ordered, the way a
        // radio station advertises its settings.
        self::assertInstanceOf(MetatagSortByValue::class, $model->tracksSortByValues[0]);
        self::assertTrue($model->tracksSortByValues[0]->active);
        self::assertSame('new', $model->albumsSortByValues[0]->value);
        self::assertCount(1, $model->playlistsSortByValues);

        self::assertSame([], $model->tracks);
        self::assertSame([], $model->composers);
        self::assertSame([], $model->promotions);
        self::assertSame([], $model->features);
    }

    protected function equalityTriple(): array
    {
        return [new Metatag('run'), new Metatag('run', new MetatagTitle('Для бега')), new Metatag('sleep')];
    }
}
