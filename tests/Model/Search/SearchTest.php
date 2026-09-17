<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Search;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Model\Search\Best;
use LuckyWins\YandexMusic\Model\Search\Search;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\Video;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Search::class)]
final class SearchTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Search::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'searchRequestId' => '1559390400000000-1',
            'text' => 'нирвана',
            'type' => 'all',
            'page' => 0,
            'perPage' => 20,
            'misspellResult' => 'нирвана',
            'misspellOriginal' => 'нирванна',
            'misspellCorrected' => true,
            'nocorrect' => false,
            'best' => ['type' => 'track', 'result' => ['id' => 31190260, 'title' => 'Нирвана']],
            'tracks' => ['type' => 'track', 'total' => 120, 'perPage' => 20, 'order' => 0, 'results' => [
                ['id' => 31190260, 'title' => 'Нирвана'],
            ]],
            'albums' => ['type' => 'album', 'total' => 5, 'results' => [['id' => 4243617, 'title' => 'Hajime']]],
            'artists' => ['type' => 'artist', 'total' => 2, 'results' => [['id' => 4611844, 'name' => 'Miyagi']]],
            'playlists' => ['type' => 'playlist', 'total' => 3, 'results' => [['uid' => 1, 'kind' => 1042]]],
            'videos' => ['type' => 'video', 'total' => 1, 'results' => [['title' => 'Нирвана', 'provider' => 'youtube']]],
            // The clips set carries no type of its own, so the field name is
            // all there is to go on — checked against the live API.
            'clips' => ['total' => 4, 'perPage' => 10, 'order' => 0, 'results' => [
                ['clipId' => 91, 'title' => 'Nirvana'],
            ]],
            'users' => ['type' => 'user', 'total' => 1, 'results' => [['uid' => 503646255, 'login' => 'andreu']]],
            'podcasts' => ['type' => 'podcast', 'total' => 1, 'results' => [['id' => 99, 'title' => 'Подкаст']]],
            'podcast_episodes' => ['type' => 'podcast_episode', 'total' => 1, 'results' => [
                ['id' => 100, 'title' => 'Выпуск'],
            ]],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['text' => 'нирвана'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Search::class, $model);

        self::assertSame('1559390400000000-1', $model->searchRequestId);
        self::assertSame('нирвана', $model->text);
        self::assertSame('all', $model->type);
        self::assertSame(0, $model->page);
        self::assertSame(20, $model->perPage);
        self::assertSame('нирвана', $model->misspellResult);
        self::assertSame('нирванна', $model->misspellOriginal);
        self::assertTrue($model->misspellCorrected);
        self::assertFalse($model->nocorrect);

        self::assertInstanceOf(Best::class, $model->best);
        self::assertInstanceOf(Track::class, $model->best->result);

        self::assertInstanceOf(Track::class, $model->tracks?->results[0]);
        self::assertSame(120, $model->tracks->total);
        self::assertInstanceOf(Album::class, $model->albums?->results[0]);
        self::assertInstanceOf(Artist::class, $model->artists?->results[0]);
        self::assertInstanceOf(Playlist::class, $model->playlists?->results[0]);
        self::assertInstanceOf(Video::class, $model->videos?->results[0]);
        self::assertInstanceOf(Clip::class, $model->clips?->results[0]);
        self::assertSame('clip', $model->clips->type, 'no type in the payload, so the field name stands in');
        self::assertInstanceOf(User::class, $model->users?->results[0]);

        // Podcasts are albums and their episodes are tracks.
        self::assertInstanceOf(Album::class, $model->podcasts?->results[0]);
        self::assertInstanceOf(Track::class, $model->podcastEpisodes?->results[0]);
    }

    /**
     * The API spells this one with an underscore while the property is
     * camelCase, which the key matching handles — and which is the reason
     * prepare() cannot read the raw keys verbatim.
     */
    public function testPodcastEpisodesArriveUnderASnakeCaseKey(): void
    {
        $model = Search::fromApi([
            'text' => 'подкаст',
            'podcast_episodes' => ['type' => 'podcast_episode', 'results' => [['id' => 100, 'title' => 'Выпуск']]],
        ], self::client());

        self::assertInstanceOf(Search::class, $model);
        self::assertNotNull($model->podcastEpisodes);
        self::assertCount(1, $model->podcastEpisodes->results);
    }

    /**
     * A search for one type fills that set and leaves the others null, rather
     * than handing back empty sets that were never sent.
     */
    public function testUnsentSetsStayNull(): void
    {
        $model = Search::fromApi([
            'text' => 'нирвана',
            'type' => 'track',
            'tracks' => ['type' => 'track', 'results' => [['id' => 31190260]]],
        ], self::client());

        self::assertInstanceOf(Search::class, $model);
        self::assertNotNull($model->tracks);
        self::assertNull($model->albums);
        self::assertNull($model->artists);
        self::assertNull($model->playlists);
        self::assertNull($model->videos);
        self::assertNull($model->clips);
        self::assertNull($model->users);
        self::assertNull($model->podcasts);
        self::assertNull($model->podcastEpisodes);
    }

    public function testSetsListsOnlyWhatWasFound(): void
    {
        $model = Search::fromApi([
            'text' => 'нирвана',
            'tracks' => ['type' => 'track', 'results' => [['id' => 31190260]]],
            'albums' => ['type' => 'album', 'total' => 0, 'results' => []],
        ], self::client());

        self::assertInstanceOf(Search::class, $model);
        self::assertSame(['tracks'], array_keys($model->sets()));
    }

    /**
     * The one response shape the reference library only documents: playlists
     * arriving in the artists field. Reading them as artists would throw.
     */
    public function testAMisfiledSetIsReadByItsOwnType(): void
    {
        $model = Search::fromApi([
            'text' => 'нирвана',
            'artists' => [
                'type' => 'playlist',
                'results' => [['uid' => 503646255, 'kind' => 1042, 'title' => 'Плейлист дня']],
            ],
        ], self::client());

        self::assertInstanceOf(Search::class, $model);
        self::assertNotNull($model->artists);
        self::assertSame('playlist', $model->artists->type);
        self::assertInstanceOf(Playlist::class, $model->artists->results[0]);
    }

    protected function equalityTriple(): array
    {
        return [
            new Search('1559390400000000-1', 'нирвана'),
            new Search('1559390400000000-1', 'нирвана', new Best('track')),
            new Search('1559390400000000-2', 'нирвана'),
        ];
    }
}
