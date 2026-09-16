<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Track::class)]
final class TrackTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Track::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 31190260,
            'title' => 'Нирвана',
            'available' => true,
            'durationMs' => 273480,
            'coverUri' => 'avatars.yandex.net/get-music-content/1/%%',
            'artists' => [
                ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль'],
                ['id' => 2521824, 'name' => 'KREC'],
            ],
            'albums' => [['id' => 33431736, 'title' => 'Hajime, Pt. 2']],
            'major' => ['id' => 1, 'name' => 'Universal'],
            'r128' => ['i' => -9.5, 'tp' => 0.3],
            'fade' => ['inStart' => 0.0, 'inStop' => 1.5],
            'lyricsInfo' => ['hasAvailableSyncLyrics' => false, 'hasAvailableTextLyrics' => true],
            'normalization' => ['gain' => -3.5, 'peak' => 32767],
            'derivedColors' => ['average' => '#112233'],
            'smartPreviewParams' => ['durationMs' => 30000, 'fade' => ['inStart' => 0.5]],
            'poetryLoverMatches' => [['begin' => 0, 'end' => 12, 'line' => 1]],
            'contentWarning' => 'explicit',
            'explicit' => true,
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 31190260];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Track::class, $model);
        self::assertSame(31190260, $model->id);
        self::assertSame('Нирвана', $model->title);
        self::assertSame(273480, $model->durationMs);

        self::assertCount(2, $model->artists);
        self::assertInstanceOf(Artist::class, $model->artists[0]);
        self::assertSame('Miyagi & Эндшпиль', $model->artists[0]->name);

        self::assertCount(1, $model->albums);
        self::assertInstanceOf(Album::class, $model->albums[0]);
        self::assertSame(33431736, $model->albums[0]->id);

        self::assertSame(1, $model->major?->id);
        self::assertSame(-9.5, $model->r128?->i);
        self::assertSame(1.5, $model->fade?->inStop);
        self::assertTrue($model->lyricsInfo?->hasAvailableTextLyrics);
        self::assertSame(32767, $model->normalization?->peak);
        self::assertSame('#112233', $model->derivedColors?->average);

        // Nesting two levels deep inside an optional branch.
        self::assertSame(0.5, $model->smartPreviewParams?->fade?->inStart);

        self::assertCount(1, $model->poetryLoverMatches);
        self::assertSame(12, $model->poetryLoverMatches[0]->end);
    }

    protected function equalityTriple(): array
    {
        return [
            new Track(31190260, 'Нирвана'),
            new Track(31190260, 'другое название'),
            new Track(99999999, 'Нирвана'),
        ];
    }

    /**
     * Playback and likes want the track paired with an album; everything else
     * wants the bare id. Getting this wrong fails quietly.
     */
    public function testCompositeIdPairsTheTrackWithItsAlbum(): void
    {
        $model = Track::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(Track::class, $model);
        self::assertSame('31190260:33431736', $model->compositeId());
    }

    public function testCompositeIdFallsBackToTheBareIdWithoutAnAlbum(): void
    {
        $model = Track::fromApi(self::requiredPayload(), self::client());

        self::assertInstanceOf(Track::class, $model);
        self::assertSame('31190260', $model->compositeId());
    }

    public function testArtistNames(): void
    {
        $model = Track::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(Track::class, $model);
        self::assertSame(['Miyagi & Эндшпиль', 'KREC'], $model->artistNames());
    }

    public function testCoverUrlFillsInTheSize(): void
    {
        $model = Track::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(Track::class, $model);
        self::assertSame(
            'https://avatars.yandex.net/get-music-content/1/400x400',
            $model->coverUrl('400x400'),
        );
        self::assertNull(Track::fromApi(['id' => 1], self::client())?->coverUrl());
    }

    /**
     * Track, Album and Artist reference one another. The schema is cyclic; the
     * data is not, because the API truncates. Descending only where a key is
     * actually present is what makes that safe.
     */
    public function testACyclicSchemaTerminatesOnFiniteData(): void
    {
        $payload = [
            'id' => 1,
            'albums' => [[
                'id' => 10,
                'volumes' => [[
                    ['id' => 2, 'albums' => [['id' => 11]]],
                ]],
                'artists' => [['id' => 100, 'popularTracks' => [['id' => 3]]]],
            ]],
            'substituted' => ['id' => 4, 'albums' => [['id' => 12]]],
        ];

        $model = Track::fromApi($payload, self::client());

        self::assertInstanceOf(Track::class, $model);
        $album = $model->albums[0] ?? null;
        self::assertNotNull($album);
        self::assertSame(10, $album->id);

        $nestedTrack = ($album->volumes ?? [])[0][0] ?? null;
        self::assertNotNull($nestedTrack);
        self::assertSame(2, $nestedTrack->id);

        $nestedAlbum = $nestedTrack->albums[0] ?? null;
        self::assertNotNull($nestedAlbum);
        self::assertSame(11, $nestedAlbum->id);

        $popular = ($album->artists[0]->popularTracks ?? [])[0] ?? null;
        self::assertNotNull($popular);
        self::assertSame(3, $popular->id);

        $substituted = $model->substituted;
        self::assertNotNull($substituted);
        self::assertSame(4, $substituted->id);

        // The descent stops where the data does.
        self::assertSame([], $nestedAlbum->artists);
        self::assertNull($substituted->substituted);
    }
}
