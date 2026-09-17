<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Likes as LikesTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Clip\ClipsWillLike;
use LuckyWins\YandexMusic\Model\Like;
use LuckyWins\YandexMusic\Model\TracksList;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LikesTrait::class)]
final class LikesTest extends TestCase
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

    /**
     * Liked tracks live one level down, in `library`, unlike every other
     * listing here.
     */
    public function testLikedTracksAreUnwrappedFromTheLibrary(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['library' => [
                'uid' => self::UID,
                'revision' => 42,
                'tracks' => [['id' => 31190260, 'albumId' => 4243617]],
            ]]]);

        $library = $this->initialized($http)->usersLikesTracks();

        self::assertInstanceOf(TracksList::class, $library);
        self::assertSame(42, $library->revision);
        self::assertSame(['31190260:4243617'], $library->compositeIds());
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/likes/tracks?if-modified-since-revision=0',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * Polling with a revision the library is already at answers with nothing,
     * which is the whole point of the parameter.
     */
    public function testAnUnchangedLibraryIsNull(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => []]);

        self::assertNull($this->initialized($http)->usersLikesTracks(ifModifiedSinceRevision: 42));
        self::assertSame(
            'if-modified-since-revision=42',
            $http->lastRequest()->getUri()->getQuery(),
        );
    }

    public function testLikedAlbumsAreStampedAsAlbums(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => [['id' => '1', 'album' => ['id' => 4243617, 'title' => 'Hajime']]]]);

        $likes = $this->initialized($http)->usersLikesAlbums();

        self::assertCount(1, $likes);
        self::assertInstanceOf(Like::class, $likes[0]);
        self::assertSame('album', $likes[0]->type);
        self::assertSame('Hajime', $likes[0]->album?->title);
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/likes/albums?rich=True',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testLikedArtistsPassTheTimestampFlag(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => [['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']]]);

        $likes = $this->initialized($http)->usersLikesArtists(withTimestamps: true);

        self::assertCount(1, $likes);
        self::assertSame('artist', $likes[0]->type);
        self::assertSame('Miyagi & Эндшпиль', $likes[0]->artist?->name);
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/likes/artists?with-timestamps=True',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testLikedPlaylistsAreTyped(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => [['id' => '1', 'playlist' => ['uid' => 1, 'kind' => 1042]]]]);

        $likes = $this->initialized($http)->usersLikesPlaylists();

        self::assertCount(1, $likes);
        self::assertSame(1042, $likes[0]->playlist?->kind);
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/likes/playlists',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * Adding hits add-multiple, removing hits remove, and several ids travel
     * comma-separated in one request.
     */
    public function testAddingAndRemovingLikes(): void
    {
        foreach ([
            ['usersLikesAlbumsAdd', 'albums', 'add-multiple', 'album-ids'],
            ['usersLikesAlbumsRemove', 'albums', 'remove', 'album-ids'],
            ['usersLikesArtistsAdd', 'artists', 'add-multiple', 'artist-ids'],
            ['usersLikesArtistsRemove', 'artists', 'remove', 'artist-ids'],
            ['usersLikesPlaylistsAdd', 'playlists', 'add-multiple', 'playlist-ids'],
            ['usersLikesPlaylistsRemove', 'playlists', 'remove', 'playlist-ids'],
        ] as [$method, $path, $action, $key]) {
            $http = (new MockHttpClient())
                ->queue(self::STATUS)
                ->queue(['result' => 'ok']);

            self::assertTrue($this->initialized($http)->{$method}(['1', '2']), $method);
            self::assertSame([$key => '1,2'], $http->formBodyAt(1), $method);
            self::assertSame(
                'https://api.music.yandex.net/users/'.self::UID.'/likes/'.$path.'/'.$action,
                (string) $http->lastRequest()->getUri(),
                $method,
            );
        }
    }

    /**
     * Track operations answer with the library's new revision instead of
     * `ok`, so success has to be recognized differently for them.
     */
    public function testLikingATrackSucceedsOnARevision(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['revision' => 43]]);

        self::assertTrue($this->initialized($http)->usersLikesTracksAdd(31190260));
        self::assertSame(['track-ids' => '31190260'], $http->formBodyAt(1));
    }

    public function testLikingATrackFailsWithoutARevision(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => []]);

        self::assertFalse($this->initialized($http)->usersLikesTracksAdd(31190260));
    }

    public function testAFailedLikeIsReported(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => 'error']);

        self::assertFalse($this->initialized($http)->usersLikesAlbumsAdd(1));
    }

    /**
     * The reference sends this parameter with underscores here and with
     * hyphens for likes; we send hyphens in both.
     */
    public function testDislikedTracksUseTheHyphenatedRevisionParameter(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['library' => ['uid' => self::UID, 'revision' => 7, 'tracks' => []]]]);

        $library = $this->initialized($http)->usersDislikesTracks(ifModifiedSinceRevision: 3);

        self::assertInstanceOf(TracksList::class, $library);
        self::assertSame(7, $library->revision);
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/dislikes/tracks?if-modified-since-revision=3',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * The API sends these as bare artists with the time they were disliked
     * beside the artist's own fields. The reference returns artists and drops
     * the time; these keep it, in the same Like objects the likes use.
     */
    public function testDislikedArtistsKeepTheirTimestamp(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => [[
                'id' => 4611844,
                'name' => 'Miyagi & Эндшпиль',
                'timestamp' => '2019-06-01T12:00:00+00:00',
            ]]]);

        $dislikes = $this->initialized($http)->usersDislikesArtists();

        self::assertCount(1, $dislikes);
        self::assertInstanceOf(Like::class, $dislikes[0]);
        self::assertSame('artist', $dislikes[0]->type);
        self::assertInstanceOf(Artist::class, $dislikes[0]->artist);
        self::assertSame('Miyagi & Эндшпиль', $dislikes[0]->artist->name);
        self::assertSame('2019-06-01T12:00:00+00:00', $dislikes[0]->timestamp);
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/dislikes/artists',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testAddingAndRemovingDislikes(): void
    {
        foreach ([
            ['usersDislikesTracksAdd', 'tracks', 'add-multiple', 'track-ids', ['revision' => 2]],
            ['usersDislikesTracksRemove', 'tracks', 'remove', 'track-ids', ['revision' => 3]],
            ['usersDislikesArtistsAdd', 'artists', 'add-multiple', 'artist-ids', 'ok'],
            ['usersDislikesArtistsRemove', 'artists', 'remove', 'artist-ids', 'ok'],
        ] as [$method, $path, $action, $key, $answer]) {
            $http = (new MockHttpClient())
                ->queue(self::STATUS)
                ->queue(['result' => $answer]);

            self::assertTrue($this->initialized($http)->{$method}(1), $method);
            self::assertSame([$key => '1'], $http->formBodyAt(1), $method);
            self::assertSame(
                'https://api.music.yandex.net/users/'.self::UID.'/dislikes/'.$path.'/'.$action,
                (string) $http->lastRequest()->getUri(),
                $method,
            );
        }
    }

    public function testLikedClipsArePaged(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => [
                'clips' => [['clipId' => 91]],
                'pager' => ['total' => 1, 'page' => 2, 'perPage' => 5],
            ]]);

        $clips = $this->initialized($http)->usersLikesClips(2, 5);

        self::assertInstanceOf(ClipsWillLike::class, $clips);
        self::assertCount(1, $clips->clips);
        self::assertSame(2, $clips->pager?->page);
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/likes/clips?page=2&pageSize=5',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * Clip likes take their id in the query string rather than the body, the
     * same oddity as joining a collective playlist.
     */
    public function testClipLikesPutTheIdInTheQuery(): void
    {
        foreach (['usersLikesClipsAdd' => 'add', 'usersLikesClipsRemove' => 'remove'] as $method => $action) {
            $http = (new MockHttpClient())
                ->queue(self::STATUS)
                ->queue(['result' => 'ok']);

            self::assertTrue($this->initialized($http)->{$method}(91), $method);
            self::assertSame('POST', $http->lastRequest()->getMethod(), $method);
            self::assertSame(
                'https://api.music.yandex.net/users/'.self::UID.'/likes/clips/'.$action.'?clip-id=91',
                (string) $http->lastRequest()->getUri(),
                $method,
            );
            self::assertSame([], $http->formBodyAt(1), $method);
        }
    }

    public function testAnotherUsersLikesCanBeRead(): void
    {
        $http = (new MockHttpClient())->queue(['result' => []]);

        $this->client($http)->usersLikesPlaylists(503646255);

        self::assertSame(
            'https://api.music.yandex.net/users/503646255/likes/playlists',
            (string) $http->lastRequest()->getUri(),
        );
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
