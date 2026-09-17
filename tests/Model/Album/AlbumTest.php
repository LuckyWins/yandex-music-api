<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Album;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Label\Label;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Album::class)]
final class AlbumTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Album::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 33431736,
            'title' => 'Hajime, Pt. 2',
            'trackCount' => 12,
            'year' => 2017,
            'genre' => 'rap',
            'coverUri' => 'avatars.yandex.net/get-music-content/2/%%',
            'artists' => [['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']],
            'labels' => [['id' => 5, 'name' => 'Hajime Records']],
            'volumes' => [
                [['id' => 1, 'title' => 'Первый'], ['id' => 2, 'title' => 'Второй']],
                [['id' => 3, 'title' => 'Со второго диска']],
            ],
            'trackPosition' => ['volume' => 1, 'index' => 7],
            'deprecation' => ['targetAlbumId' => 999, 'status' => 'replaced'],
            'actionButton' => ['text' => 'Слушать'],
            'available' => true,
            'cover' => ['type' => 'pic', 'uri' => 'avatars.invalid/%%'],
            'derivedColors' => ['average' => '#3c3c3c'],
            'trailer' => ['available' => true],
            'hasTrailer' => true,
            'childContent' => false,
            'customWave' => ['title' => 'Моя волна'],
            'pager' => ['total' => 28, 'page' => 0, 'perPage' => 20],
            'metaTagId' => 'rap',
            'sortOrder' => 'desc',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 33431736];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Album::class, $model);
        self::assertSame(33431736, $model->id);
        self::assertSame('Hajime, Pt. 2', $model->title);
        self::assertSame(2017, $model->year);
        self::assertCount(1, $model->artists);
        self::assertSame(1, $model->trackPosition?->volume);
        self::assertSame(999, $model->deprecation?->targetAlbumId);
        self::assertSame('Слушать', $model->actionButton?->text);

        // cover and coverUri carry the same art in different forms; both fill in.
        self::assertInstanceOf(Cover::class, $model->cover);
        self::assertSame('avatars.yandex.net/get-music-content/2/%%', $model->coverUri);
        self::assertSame('#3c3c3c', $model->derivedColors?->average);
        self::assertTrue($model->trailer?->available);
        self::assertTrue($model->hasTrailer);
        self::assertFalse($model->childContent);
        self::assertSame('Моя волна', $model->customWave?->title);
        self::assertSame(28, $model->pager?->total);
        self::assertSame('rap', $model->metaTagId);

        // Objects here, but see the string case below.
        self::assertCount(1, $model->labels);
        $label = $model->labels[0] ?? null;
        self::assertInstanceOf(Label::class, $label);
        self::assertSame('Hajime Records', $label->name);

        // One inner list per disc.
        $volumes = $model->volumes;
        self::assertNotNull($volumes);
        self::assertCount(2, $volumes);
        self::assertCount(2, $volumes[0] ?? []);

        $first = $volumes[0][0] ?? null;
        $secondDisc = $volumes[1][0] ?? null;
        self::assertInstanceOf(Track::class, $first);
        self::assertInstanceOf(Track::class, $secondDisc);
        self::assertSame('Первый', $first->title);
        self::assertSame('Со второго диска', $secondDisc->title);
    }

    protected function equalityTriple(): array
    {
        return [new Album(1, title: 'a'), new Album(1, title: 'b'), new Album(2, title: 'a')];
    }

    /**
     * The same field comes back as objects from one endpoint and as bare names
     * from another. Both have to work, and neither may corrupt the other.
     */
    public function testLabelsArriveAsObjectsOrAsNames(): void
    {
        $asNames = Album::fromApi(['id' => 1, 'labels' => ['Hajime', 'Universal']], self::client());

        self::assertInstanceOf(Album::class, $asNames);
        self::assertSame(['Hajime', 'Universal'], $asNames->labels);

        $asObjects = Album::fromApi(['id' => 1, 'labels' => [['id' => 5, 'name' => 'Hajime']]], self::client());

        self::assertInstanceOf(Album::class, $asObjects);
        self::assertInstanceOf(Label::class, $asObjects->labels[0] ?? null);
    }

    public function testLabelsDefaultToEmptyWhenAbsent(): void
    {
        $model = Album::fromApi(['id' => 1], self::client());

        self::assertInstanceOf(Album::class, $model);
        self::assertSame([], $model->labels);
        self::assertNull($model->volumes);
    }

    /**
     * Discs are an accident of how albums were once sold; most callers want
     * the tracks in order and do not care where one disc ends.
     */
    public function testTracksFlattensTheDiscs(): void
    {
        $model = Album::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(Album::class, $model);
        self::assertCount(3, $model->tracks());
        self::assertSame([1, 2, 3], array_map(static fn (Track $t): int|string => $t->id, $model->tracks()));
    }

    public function testTracksIsEmptyWithoutVolumes(): void
    {
        $model = Album::fromApi(['id' => 1], self::client());

        self::assertInstanceOf(Album::class, $model);
        self::assertSame([], $model->tracks());
    }
}
