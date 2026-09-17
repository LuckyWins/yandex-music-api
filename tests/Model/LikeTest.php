<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Like;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Like::class)]
final class LikeTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Like::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'type' => 'album',
            'id' => '4243617',
            'timestamp' => '2019-06-01T12:00:00+00:00',
            'album' => ['id' => 4243617, 'title' => 'Hajime'],
            'shortDescription' => 'Коротко',
            'description' => 'Подробно',
            'isPremiere' => false,
            'isBanner' => true,
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'album'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Like::class, $model);
        self::assertSame('album', $model->type);
        self::assertSame('4243617', $model->id);
        self::assertSame('2019-06-01T12:00:00+00:00', $model->timestamp);
        self::assertInstanceOf(Album::class, $model->album);
        self::assertSame('Hajime', $model->album->title);
        self::assertSame('Коротко', $model->shortDescription);
        self::assertSame('Подробно', $model->description);
        self::assertFalse($model->isPremiere);
        self::assertTrue($model->isBanner);
    }

    public function testAlbumLikesAreStampedWithTheirType(): void
    {
        $likes = Like::listOfType([
            ['id' => '1', 'timestamp' => 't', 'album' => ['id' => 1, 'title' => 'Hajime']],
            ['id' => '2', 'album' => ['id' => 2]],
        ], 'album', self::client());

        self::assertCount(2, $likes);
        self::assertSame('album', $likes[0]->type);
        self::assertInstanceOf(Album::class, $likes[0]->album);
        self::assertSame('Hajime', $likes[0]->album->title);
        self::assertNull($likes[0]->artist);
        self::assertNull($likes[0]->playlist);
    }

    public function testPlaylistLikesCarryTheWholePlaylist(): void
    {
        $likes = Like::listOfType([
            ['id' => '1', 'playlist' => ['uid' => 503646255, 'kind' => 1042, 'title' => 'Плейлист дня']],
        ], 'playlist', self::client());

        self::assertCount(1, $likes);
        self::assertInstanceOf(Playlist::class, $likes[0]->playlist);
        self::assertSame('Плейлист дня', $likes[0]->playlist->title);
        self::assertSame($likes[0]->playlist, $likes[0]->object());
    }

    /**
     * Liked artists arrive as bare artists — no wrapper, no timestamp — which
     * is why the type cannot be read off the response.
     */
    public function testBareArtistsAreWrapped(): void
    {
        $likes = Like::listOfType([
            ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль'],
        ], 'artist', self::client());

        self::assertCount(1, $likes);
        self::assertSame('artist', $likes[0]->type);
        self::assertInstanceOf(Artist::class, $likes[0]->artist);
        self::assertSame('Miyagi & Эндшпиль', $likes[0]->artist->name);
        self::assertNull($likes[0]->timestamp);
    }

    /**
     * A bare artist still carries the time it was liked, and that belongs to
     * the like rather than to the artist — the model would otherwise report it
     * as a field it does not know.
     */
    public function testATimestampBesideABareArtistBelongsToTheLike(): void
    {
        $likes = Like::listOfType([
            ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль', 'timestamp' => '2019-06-01T12:00:00+00:00'],
        ], 'artist', self::client());

        self::assertCount(1, $likes);
        self::assertSame('2019-06-01T12:00:00+00:00', $likes[0]->timestamp);
        self::assertSame('Miyagi & Эндшпиль', $likes[0]->artist?->name);
    }

    /**
     * The wrapped form has to keep working too: the same endpoint sends it
     * when timestamps were asked for.
     */
    public function testWrappedArtistsAreReadTheSameWay(): void
    {
        $likes = Like::listOfType([
            ['timestamp' => 't', 'artist' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']],
        ], 'artist', self::client());

        self::assertCount(1, $likes);
        self::assertSame('t', $likes[0]->timestamp);
        self::assertInstanceOf(Artist::class, $likes[0]->artist);
        self::assertSame('Miyagi & Эндшпиль', $likes[0]->artist->name);
    }

    public function testAnythingButAListIsNoLikes(): void
    {
        self::assertSame([], Like::listOfType(null, 'album', self::client()));
        self::assertSame([], Like::listOfType('nonsense', 'album', self::client()));
        self::assertSame([], Like::listOfType([], 'album', self::client()));
    }

    protected function equalityTriple(): array
    {
        return [
            new Like('album', '1'),
            new Like('album', '1', 'другое время'),
            new Like('album', '2'),
        ];
    }
}
