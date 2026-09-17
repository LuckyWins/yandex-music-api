<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistDiff;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PlaylistDiff::class)]
final class PlaylistDiffTest extends TestCase
{
    public function testInsertBuildsTheOperationTheApiWants(): void
    {
        $diff = (new PlaylistDiff())->insert(0, new TrackId(id: 31190260, albumId: 4243617));

        self::assertSame(
            '[{"op":"insert","at":0,"tracks":[{"id":31190260,"albumId":4243617}]}]',
            $diff->toJson(),
        );
    }

    public function testSeveralTracksGoIntoOneOperation(): void
    {
        $diff = (new PlaylistDiff())->insert(
            2,
            new TrackId(id: 1, albumId: 10),
            new TrackId(id: 2, albumId: 20),
        );

        self::assertSame(
            '[{"op":"insert","at":2,"tracks":[{"id":1,"albumId":10},{"id":2,"albumId":20}]}]',
            $diff->toJson(),
        );
    }

    public function testDeleteBuildsARange(): void
    {
        self::assertSame(
            '[{"op":"delete","from":3,"to":5}]',
            (new PlaylistDiff())->delete(3, 5)->toJson(),
        );
    }

    /**
     * The API applies operations in order, so the builder must not reorder
     * them: deleting before inserting means different indices.
     */
    public function testOperationsKeepTheirOrder(): void
    {
        $diff = (new PlaylistDiff())
            ->delete(0, 1)
            ->insert(0, new TrackId(id: 1, albumId: 10));

        self::assertSame(
            '[{"op":"delete","from":0,"to":1},{"op":"insert","at":0,"tracks":[{"id":1,"albumId":10}]}]',
            $diff->toJson(),
        );
    }

    public function testATrackWithoutItsAlbumIsRefused(): void
    {
        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('both its id and its album id');

        (new PlaylistDiff())->insert(0, new TrackId(id: 31190260));
    }

    public function testAnInsertWithoutTracksIsRefused(): void
    {
        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('at least one track');

        (new PlaylistDiff())->insert(0);
    }

    public function testADescendingRangeIsRefused(): void
    {
        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('ascending range');

        (new PlaylistDiff())->delete(5, 3);
    }

    /**
     * An empty diff would be accepted by the API and change nothing, which is
     * a bug in the caller rather than something to send.
     */
    public function testAnEmptyDiffIsRefused(): void
    {
        $diff = new PlaylistDiff();

        self::assertTrue($diff->isEmpty());

        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('at least one operation');

        $diff->toJson();
    }

    public function testOperationsAreReadable(): void
    {
        $diff = (new PlaylistDiff())->delete(0, 1);

        self::assertSame([['op' => 'delete', 'from' => 0, 'to' => 1]], $diff->operations());
        self::assertFalse($diff->isEmpty());
    }
}
