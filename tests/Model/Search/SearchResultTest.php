<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Search;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Search\SearchResult;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SearchResult::class)]
final class SearchResultTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return SearchResult::class;
    }

    protected static function fullPayload(): array
    {
        return ['type' => 'track', 'total' => 120, 'perPage' => 20, 'order' => 0, 'results' => []];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'track'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(SearchResult::class, $model);
        self::assertSame('track', $model->type);
        self::assertSame(120, $model->total);
        self::assertSame(20, $model->perPage);
        self::assertSame(0, $model->order);
    }

    public function testResultsAreBuiltAsTheNamedModel(): void
    {
        $set = SearchResult::of(Track::class, 'track', [
            'type' => 'track',
            'total' => 2,
            'perPage' => 20,
            'order' => 0,
            'results' => [
                ['id' => 31190260, 'title' => 'Нирвана'],
                ['id' => 31190261, 'title' => 'Тёмный рыцарь'],
            ],
        ], self::client());

        self::assertInstanceOf(SearchResult::class, $set);
        self::assertSame(2, $set->total);
        self::assertCount(2, $set->results);
        self::assertInstanceOf(Track::class, $set->results[0]);
        self::assertSame('Нирвана', $set->results[0]->title);
    }

    public function testAlbumsToo(): void
    {
        $set = SearchResult::of(Album::class, 'album', [
            'type' => 'album',
            'results' => [['id' => 4243617, 'title' => 'Hajime']],
        ], self::client());

        self::assertInstanceOf(SearchResult::class, $set);
        self::assertInstanceOf(Album::class, $set->results[0]);
    }

    public function testAnEmptySetIsStillASet(): void
    {
        $set = SearchResult::of(Track::class, 'track', ['type' => 'track', 'total' => 0, 'results' => []], self::client());

        self::assertInstanceOf(SearchResult::class, $set);
        self::assertSame([], $set->results);
        self::assertSame(0, $set->total);
    }

    public function testNothingAtAllIsNull(): void
    {
        self::assertNull(SearchResult::of(Track::class, 'track', null, self::client()));
        self::assertNull(SearchResult::of(Track::class, 'track', [], self::client()));
        self::assertNull(SearchResult::of(Track::class, 'track', 'nonsense', self::client()));
    }

    /**
     * The service has been seen putting playlists in the artists field.
     * Following the field name there would mean building an Artist out of a
     * playlist, which throws; the response's own type wins instead.
     */
    public function testTheResponsesOwnTypeWinsOverTheExpectedOne(): void
    {
        $set = SearchResult::of(Artist::class, 'artist', [
            'type' => 'playlist',
            'results' => [['uid' => 503646255, 'kind' => 1042, 'title' => 'Плейлист дня']],
        ], self::client());

        self::assertInstanceOf(SearchResult::class, $set);
        self::assertSame('playlist', $set->type, 'the type says what was actually built');
        self::assertInstanceOf(Playlist::class, $set->results[0]);
    }

    /**
     * A type nobody knows falls back to what the field was expected to hold,
     * which is the best guess available.
     */
    public function testAnUnknownTypeFallsBackToTheExpectedModel(): void
    {
        $set = SearchResult::of(Track::class, 'track', [
            'type' => 'hologram',
            'results' => [['id' => 31190260, 'title' => 'Нирвана']],
        ], self::client());

        self::assertInstanceOf(SearchResult::class, $set);
        self::assertSame('hologram', $set->type);
        self::assertInstanceOf(Track::class, $set->results[0]);
    }

    public function testATypelessSetIsReadAsTheFieldSuggests(): void
    {
        $set = SearchResult::of(Track::class, 'track', [
            'results' => [['id' => 31190260, 'title' => 'Нирвана']],
        ], self::client());

        self::assertInstanceOf(SearchResult::class, $set);
        self::assertSame('track', $set->type);
        self::assertInstanceOf(Track::class, $set->results[0]);
    }

    protected function equalityTriple(): array
    {
        return [
            new SearchResult('track', 120, 20, 0),
            new SearchResult('track', 120, 20, 0, [new Track(1)]),
            new SearchResult('track', 120, 20, 1),
        ];
    }
}
