<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Playlists as PlaylistsTrait;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Playlist\GeneratedPlaylist;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistDiff;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistRecommendations;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistSimilarEntities;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistsList;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistTrailer;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PlaylistsTrait::class)]
final class PlaylistsTest extends TestCase
{
    private const UID = 1130000002804451;

    private const STATUS = [
        'result' => [
            'account' => [
                'now' => '2026-09-17T12:00:00+00:00',
                'serviceAvailable' => true,
                'uid' => self::UID,
                'login' => 'user@yandex.ru',
            ],
        ],
    ];

    public function testOnePlaylistIsFetchedByKind(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['uid' => self::UID, 'kind' => 1042, 'title' => 'Плейлист дня']]);

        $playlist = $this->initialized($http)->usersPlaylists(1042);

        self::assertInstanceOf(Playlist::class, $playlist);
        self::assertSame('Плейлист дня', $playlist->title);
        self::assertSame('GET', $http->lastRequest()->getMethod());
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/playlists/1042',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * The reference returns either a playlist or a list from one method,
     * depending on what was passed. Here they are two methods, and this is
     * the one that posts.
     */
    public function testSeveralPlaylistsArePostedAsKinds(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => [['kind' => 1042], ['kind' => 1043]]]);

        $playlists = $this->initialized($http)->usersPlaylistsMany([1042, 1043]);

        self::assertCount(2, $playlists);
        self::assertSame('POST', $http->lastRequest()->getMethod());
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/playlists',
            (string) $http->lastRequest()->getUri(),
        );
        self::assertSame(['kinds' => '1042,1043'], $http->formBodyAt(1));
    }

    public function testAnotherUsersPlaylistsCanBeRead(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['kind' => 7]]);

        $this->client($http)->usersPlaylists(7, 503646255);

        self::assertSame(
            'https://api.music.yandex.net/users/503646255/playlists/7',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testPlaylistListIsTyped(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => [['kind' => 1042, 'title' => 'Первый'], ['kind' => 1043]]]);

        $playlists = $this->initialized($http)->usersPlaylistsList();

        self::assertCount(2, $playlists);
        self::assertSame('Первый', $playlists[0]->title);
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/playlists/list',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testKindsComeBackAsIntegers(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => [1042, 1043, 3]]);

        self::assertSame([1042, 1043, 3], $this->initialized($http)->usersPlaylistsKinds());
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/playlists/list/kinds',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testCreateSendsTitleAndVisibility(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['kind' => 1042, 'title' => 'Новый', 'visibility' => 'private']]);

        $playlist = $this->initialized($http)->usersPlaylistsCreate('Новый', 'private');

        self::assertInstanceOf(Playlist::class, $playlist);
        self::assertSame('private', $playlist->visibility);
        self::assertSame(['title' => 'Новый', 'visibility' => 'private'], $http->formBodyAt(1));
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/playlists/create',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testDeleteReportsWhetherItWorked(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => 'ok']);

        self::assertTrue($this->initialized($http)->usersPlaylistsDelete(1042));
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/playlists/1042/delete',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testDeleteReportsFailure(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => 'error']);

        self::assertFalse($this->initialized($http)->usersPlaylistsDelete(1042));
    }

    /**
     * Name, visibility and description are one endpoint shape with three
     * paths, and all three carry the new value under `value`.
     */
    public function testSingleValuedChanges(): void
    {
        foreach ([
            ['usersPlaylistsName', 'Другое имя', 'name'],
            ['usersPlaylistsVisibility', 'private', 'visibility'],
            ['usersPlaylistsDescription', 'Про что этот плейлист', 'description'],
        ] as [$method, $value, $path]) {
            $http = (new MockHttpClient())
                ->queue(self::STATUS)
                ->queue(['result' => ['kind' => 1042]]);

            $playlist = $this->initialized($http)->{$method}(1042, $value);

            self::assertInstanceOf(Playlist::class, $playlist, $method);
            self::assertSame(['value' => $value], $http->formBodyAt(1), $method);
            self::assertSame(
                'https://api.music.yandex.net/users/'.self::UID.'/playlists/1042/'.$path,
                (string) $http->lastRequest()->getUri(),
                $method,
            );
        }
    }

    public function testChangeSendsTheDiffAndTheRevision(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['kind' => 1042, 'revision' => 5]]);

        $diff = (new PlaylistDiff())->delete(0, 1);
        $playlist = $this->initialized($http)->usersPlaylistsChange(1042, $diff, 4);

        self::assertInstanceOf(Playlist::class, $playlist);
        self::assertSame(5, $playlist->revision);
        self::assertSame(
            ['diff' => '[{"op":"delete","from":0,"to":1}]', 'revision' => '4'],
            $http->formBodyAt(1),
        );
    }

    /**
     * Without a revision the current one has to be read first — an extra
     * request, and the reason passing one is better.
     */
    public function testChangeWithoutARevisionReadsTheCurrentOne(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['kind' => 1042, 'revision' => 9]])
            ->queue(['result' => ['kind' => 1042, 'revision' => 10]]);

        $this->initialized($http)->usersPlaylistsChange(1042, (new PlaylistDiff())->delete(0, 1));

        self::assertSame(3, $http->requestCount());
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/playlists/1042',
            (string) $http->requestAt(1)->getUri(),
        );
        self::assertSame('9', $http->formBodyAt(2)['revision'] ?? null);
    }

    public function testChangeFailsLoudlyWhenTheRevisionCannotBeRead(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['kind' => 1042]]);

        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('Could not read the current revision');

        $this->initialized($http)->usersPlaylistsChange(1042, (new PlaylistDiff())->delete(0, 1));
    }

    public function testInsertTrackBuildsTheDiff(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['kind' => 1042, 'revision' => 6]]);

        $this->initialized($http)->usersPlaylistsInsertTrack(
            1042,
            new TrackId(id: 31190260, albumId: 4243617),
            at: 2,
            revision: 5,
        );

        self::assertSame(
            '[{"op":"insert","at":2,"tracks":[{"id":31190260,"albumId":4243617}]}]',
            $http->formBodyAt(1)['diff'] ?? null,
        );
    }

    public function testDeleteTrackBuildsTheDiff(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['kind' => 1042]]);

        $this->initialized($http)->usersPlaylistsDeleteTrack(1042, 3, 5, revision: 5);

        self::assertSame('[{"op":"delete","from":3,"to":5}]', $http->formBodyAt(1)['diff'] ?? null);
    }

    public function testRecommendationsAreTyped(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['batchId' => '1', 'tracks' => [['id' => 1, 'title' => 'Нирвана']]]]);

        $recommendations = $this->initialized($http)->usersPlaylistsRecommendations(1042);

        self::assertInstanceOf(PlaylistRecommendations::class, $recommendations);
        self::assertCount(1, $recommendations->tracks);
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/playlists/1042/recommendations',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testTrailerIsTyped(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['shareable' => true, 'trailer' => ['title' => 'О плейлисте']]]);

        $trailer = $this->initialized($http)->usersPlaylistsTrailer(1042);

        self::assertInstanceOf(PlaylistTrailer::class, $trailer);
        self::assertTrue($trailer->shareable);
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/playlists/1042/trailer',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testPlaylistByUuid(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['playlistUuid' => 'abc', 'title' => 'Чужой']]);

        $playlist = $this->client($http)->playlist('abc');

        self::assertInstanceOf(Playlist::class, $playlist);
        self::assertSame('Чужой', $playlist->title);
        self::assertSame('https://api.music.yandex.net/playlist/abc', (string) $http->lastRequest()->getUri());
    }

    public function testSimilarEntities(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['items' => [['type' => 'wave']]]]);

        $similar = $this->client($http)->playlistSimilarEntities('abc');

        self::assertInstanceOf(PlaylistSimilarEntities::class, $similar);
        self::assertCount(1, $similar->items);
        self::assertSame(
            'https://api.music.yandex.net/playlist/abc/similar-entities',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * The pairs go in the query string. They are owner-and-kind pairs rather
     * than uuids, despite the parameter's name — checked against the live API,
     * which refuses a uuid here with a validation error.
     */
    public function testPlaylistsPutTheirPairsInTheQueryString(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['playlists' => [['kind' => 1042]]]]);

        $list = $this->client($http)->playlists(['1:1042', '1:1043']);

        self::assertInstanceOf(PlaylistsList::class, $list);
        self::assertCount(1, $list->playlists);
        self::assertSame('GET', $http->lastRequest()->getMethod());
        self::assertSame(
            'https://api.music.yandex.net/playlists?playlistIds=1%3A1042%2C1%3A1043',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testPlaylistsListPostsOwnerKindPairs(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [['kind' => 1042], ['kind' => 1043]]]);

        $playlists = $this->client($http)->playlistsList(['1:1042', '1:1043']);

        self::assertCount(2, $playlists);
        self::assertSame('POST', $http->lastRequest()->getMethod());
        self::assertSame('https://api.music.yandex.net/playlists/list', (string) $http->lastRequest()->getUri());
        self::assertSame(['playlist-ids' => '1:1042,1:1043'], $http->formBodyAt(0));
    }

    public function testPersonalPlaylistIsTyped(): void
    {
        $http = (new MockHttpClient())->queue([
            'result' => ['type' => 'playlistOfTheDay', 'ready' => true, 'data' => ['kind' => 1042]],
        ]);

        $generated = $this->client($http)->playlistsPersonal('playlistOfTheDay');

        self::assertInstanceOf(GeneratedPlaylist::class, $generated);
        self::assertSame(1042, $generated->data?->kind);
        self::assertSame(
            'https://api.music.yandex.net/playlists/personal/playlistOfTheDay',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * The odd one out: a POST whose arguments the API only accepts in the
     * query string.
     */
    public function testCollectiveJoinPutsItsArgumentsInTheQuery(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'ok']);

        self::assertTrue($this->client($http)->playlistsCollectiveJoin(503646255, 'invite-token'));

        $request = $http->lastRequest();

        self::assertSame('POST', $request->getMethod());
        self::assertSame('uid=503646255&token=invite-token', $request->getUri()->getQuery());
        self::assertSame([], $http->formBodyAt(0));
    }

    /**
     * Acting on behalf of a user needs the account loaded, and saying so is
     * better than sending a request to /users//playlists.
     */
    public function testWithoutInitTheFailureIsClear(): void
    {
        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('No account is loaded');

        $this->client(new MockHttpClient())->usersPlaylistsList();
    }

    private function initialized(MockHttpClient $http): Client
    {
        return $this->client($http)->init();
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
