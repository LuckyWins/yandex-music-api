<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\MusicHistory;

use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MusicHistoryQuery::class)]
final class MusicHistoryQueryTest extends TestCase
{
    /**
     * A track is only identified together with its album, which is why this
     * takes both rather than accepting one.
     */
    public function testATrackNeedsItsAlbum(): void
    {
        $query = (new MusicHistoryQuery())->track(31190260, 4243617);

        self::assertSame([
            'items' => [[
                'type' => 'track',
                'data' => ['itemId' => ['trackId' => '31190260', 'albumId' => '4243617']],
            ]],
        ], $query->toArray());
    }

    public function testEveryOtherKind(): void
    {
        $query = (new MusicHistoryQuery())
            ->album(4243617)
            ->artist(4611844)
            ->playlist(503646255, 1042)
            ->wave('user:onyourwave');

        self::assertSame([
            ['type' => 'album', 'data' => ['itemId' => ['id' => '4243617']]],
            ['type' => 'artist', 'data' => ['itemId' => ['id' => '4611844']]],
            ['type' => 'playlist', 'data' => ['itemId' => ['uid' => 503646255, 'kind' => 1042]]],
            ['type' => 'wave', 'data' => ['itemId' => ['seeds' => ['user:onyourwave']]]],
        ], $query->toArray()['items']);
    }

    /**
     * Seeds travel as a list even when there is one of them, the way pinning
     * a wave needs them to.
     */
    public function testSeedsAreAlwaysAList(): void
    {
        $query = (new MusicHistoryQuery())->wave(['user:onyourwave', 'genre:allrock']);

        self::assertSame(
            ['seeds' => ['user:onyourwave', 'genre:allrock']],
            $query->toArray()['items'][0]['data']['itemId'],
        );
    }

    public function testTheOrderAsked(): void
    {
        $query = (new MusicHistoryQuery())->artist(1)->album(2)->track(3, 4);

        $types = array_map(static fn (array $item): string => $item['type'], $query->toArray()['items']);

        self::assertSame(['artist', 'album', 'track'], $types);
    }

    public function testAnEmptyQueryIsRefused(): void
    {
        $query = new MusicHistoryQuery();

        self::assertTrue($query->isEmpty());

        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('nothing at all');

        $query->toArray();
    }
}
