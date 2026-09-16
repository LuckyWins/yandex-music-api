<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Artist::class)]
final class ArtistTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Artist::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 4611844,
            'name' => 'Miyagi & Эндшпиль',
            'various' => false,
            'composer' => false,
            'genres' => ['rap', 'rusrap'],
            'cover' => ['type' => 'from-artist-photos', 'uri' => 'avatars.yandex.net/get-music-content/3/%%'],
            'counts' => ['tracks' => 120, 'directAlbums' => 8, 'alsoAlbums' => 30, 'alsoTracks' => 15],
            'ratings' => ['month' => 42, 'week' => 17],
            'links' => [['title' => 'Сайт', 'href' => 'https://example.invalid', 'type' => 'official']],
            'popularTracks' => [['id' => 1, 'title' => 'Нирвана']],
            'description' => ['text' => 'Дуэт из Владикавказа'],
            'contentRestrictions' => ['available' => true],
            'likesCount' => 900000,
            'derivedColors' => ['average' => '#3c3c3c'],
            'trailer' => ['available' => false],
            'hasTrailer' => false,
            'extraActions' => [],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 4611844];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Artist::class, $model);
        self::assertSame(4611844, $model->id);
        self::assertSame('Miyagi & Эндшпиль', $model->name);
        self::assertSame(['rap', 'rusrap'], $model->genres);
        self::assertInstanceOf(Cover::class, $model->cover);
        self::assertSame(120, $model->counts?->tracks);
        self::assertSame(42, $model->ratings?->month);
        self::assertCount(1, $model->links ?? []);
        $link = ($model->links ?? [])[0] ?? null;
        self::assertNotNull($link);
        self::assertSame('official', $link->type);

        self::assertCount(1, $model->popularTracks ?? []);
        self::assertInstanceOf(Track::class, ($model->popularTracks ?? [])[0] ?? null);
        self::assertSame('Дуэт из Владикавказа', $model->description?->text);
        self::assertTrue($model->contentRestrictions?->available);
        self::assertSame('#3c3c3c', $model->derivedColors?->average);
        self::assertFalse($model->trailer?->available);
        self::assertFalse($model->hasTrailer);
        self::assertSame([], $model->extraActions);
    }

    protected function equalityTriple(): array
    {
        return [new Artist(1, name: 'a'), new Artist(1, name: 'a'), new Artist(2, name: 'a')];
    }

    /**
     * A credit line is not a list of artists: it alternates artists with the
     * words joining them, and only the content says which is which.
     */
    public function testDecomposedMixesArtistsWithJoiningWords(): void
    {
        $model = Artist::fromApi([
            'id' => 1,
            'name' => 'Miyagi & Эндшпиль',
            'decomposed' => [
                ['id' => 10, 'name' => 'Miyagi'],
                '&',
                ['id' => 11, 'name' => 'Эндшпиль'],
            ],
        ], self::client());

        self::assertInstanceOf(Artist::class, $model);
        $decomposed = $model->decomposed;
        self::assertNotNull($decomposed);
        self::assertCount(3, $decomposed);

        $first = $decomposed[0] ?? null;
        self::assertInstanceOf(Artist::class, $first);
        self::assertSame('Miyagi', $first->name);
        self::assertSame('&', $decomposed[1] ?? null);
        self::assertInstanceOf(Artist::class, $decomposed[2] ?? null);

        self::assertSame('Miyagi & Эндшпиль', $model->creditLine());
    }

    public function testCreditLineFallsBackToTheNameWhenNotDecomposed(): void
    {
        $model = Artist::fromApi(['id' => 1, 'name' => 'KREC'], self::client());

        self::assertInstanceOf(Artist::class, $model);
        self::assertNull($model->decomposed);
        self::assertSame('KREC', $model->creditLine());
    }
}
