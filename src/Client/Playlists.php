<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Playlist\GeneratedPlaylist;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistDiff;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistRecommendations;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistSimilarEntities;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistsList;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistTrailer;

/**
 * Playlists.
 *
 * A playlist is identified by its owner and its `kind`, which is unique per
 * user rather than globally. Methods that take a `$userId` default to the
 * account this client is authorized as, so reading someone else's public
 * playlist means passing their id explicitly.
 */
trait Playlists
{
    /**
     * One playlist of a user's.
     */
    public function usersPlaylists(string|int $kind, string|int|null $userId = null): ?Playlist
    {
        $userId ??= $this->accountUid();

        return Playlist::fromApi(
            $this->request->get($this->getBaseUrl().'/users/'.$userId.'/playlists/'.$kind),
            $this,
        );
    }

    /**
     * Several playlists of one user's, in one request.
     *
     * @param list<string|int> $kinds
     *
     * @return list<Playlist>
     */
    public function usersPlaylistsMany(array $kinds, string|int|null $userId = null): array
    {
        $userId ??= $this->accountUid();

        $result = $this->request->post($this->getBaseUrl().'/users/'.$userId.'/playlists', [
            'kinds' => implode(',', $kinds),
        ]);

        return Playlist::listFromApi($result, $this);
    }

    /**
     * Every playlist a user has.
     *
     * @return list<Playlist>
     */
    public function usersPlaylistsList(string|int|null $userId = null): array
    {
        $userId ??= $this->accountUid();

        return Playlist::listFromApi(
            $this->request->get($this->getBaseUrl().'/users/'.$userId.'/playlists/list'),
            $this,
        );
    }

    /**
     * The kinds of a user's playlists and nothing else.
     *
     * Cheaper than usersPlaylistsList() when all you need is what exists.
     *
     * @return list<int>
     */
    public function usersPlaylistsKinds(string|int|null $userId = null): array
    {
        $userId ??= $this->accountUid();

        $result = $this->request->get($this->getBaseUrl().'/users/'.$userId.'/playlists/list/kinds');

        if (!is_array($result)) {
            return [];
        }

        return array_values(array_filter($result, is_int(...)));
    }

    /**
     * Create a playlist.
     *
     * @param string $visibility public or private
     */
    public function usersPlaylistsCreate(
        string $title,
        string $visibility = 'public',
        string|int|null $userId = null,
    ): ?Playlist {
        $userId ??= $this->accountUid();

        $result = $this->request->post($this->getBaseUrl().'/users/'.$userId.'/playlists/create', [
            'title' => $title,
            'visibility' => $visibility,
        ]);

        return Playlist::fromApi($result, $this);
    }

    /**
     * Delete a playlist. There is no undo.
     */
    public function usersPlaylistsDelete(string|int $kind, string|int|null $userId = null): bool
    {
        $userId ??= $this->accountUid();

        $result = $this->request->post(
            $this->getBaseUrl().'/users/'.$userId.'/playlists/'.$kind.'/delete',
        );

        return 'ok' === $result;
    }

    /**
     * Rename a playlist.
     */
    public function usersPlaylistsName(
        string|int $kind,
        string $name,
        string|int|null $userId = null,
    ): ?Playlist {
        return $this->setPlaylistValue($kind, 'name', $name, $userId);
    }

    /**
     * Make a playlist public or private.
     *
     * @param string $visibility public or private
     */
    public function usersPlaylistsVisibility(
        string|int $kind,
        string $visibility,
        string|int|null $userId = null,
    ): ?Playlist {
        return $this->setPlaylistValue($kind, 'visibility', $visibility, $userId);
    }

    /**
     * Set a playlist's description. An empty string clears it.
     */
    public function usersPlaylistsDescription(
        string|int $kind,
        string $description,
        string|int|null $userId = null,
    ): ?Playlist {
        return $this->setPlaylistValue($kind, 'description', $description, $userId);
    }

    /**
     * Apply a set of changes to a playlist's contents.
     *
     * Revisions are how the API detects a lost update: pass the revision the
     * change was built against and a concurrent edit makes it fail rather
     * than silently overwrite. When none is given the current one is read
     * first, which costs a request and races with anyone else editing.
     */
    public function usersPlaylistsChange(
        string|int $kind,
        PlaylistDiff $diff,
        ?int $revision = null,
        string|int|null $userId = null,
    ): ?Playlist {
        $userId ??= $this->accountUid();
        $revision ??= $this->currentPlaylistRevision($kind, $userId);

        $result = $this->request->post($this->getBaseUrl().'/users/'.$userId.'/playlists/'.$kind.'/change', [
            'diff' => $diff->toJson(),
            'revision' => $revision,
        ]);

        return Playlist::fromApi($result, $this);
    }

    /**
     * Insert tracks at a position in a playlist. Index 0 puts them on top.
     */
    public function usersPlaylistsInsertTrack(
        string|int $kind,
        TrackId $track,
        int $at = 0,
        ?int $revision = null,
        string|int|null $userId = null,
    ): ?Playlist {
        return $this->usersPlaylistsChange(
            $kind,
            (new PlaylistDiff())->insert($at, $track),
            $revision,
            $userId,
        );
    }

    /**
     * Remove the tracks in a range of positions: from $from up to but not
     * including $to.
     */
    public function usersPlaylistsDeleteTrack(
        string|int $kind,
        int $from,
        int $to,
        ?int $revision = null,
        string|int|null $userId = null,
    ): ?Playlist {
        return $this->usersPlaylistsChange(
            $kind,
            (new PlaylistDiff())->delete($from, $to),
            $revision,
            $userId,
        );
    }

    /**
     * Tracks the service suggests adding to a playlist.
     */
    public function usersPlaylistsRecommendations(
        string|int $kind,
        string|int|null $userId = null,
    ): ?PlaylistRecommendations {
        $userId ??= $this->accountUid();

        return PlaylistRecommendations::fromApi(
            $this->request->get($this->getBaseUrl().'/users/'.$userId.'/playlists/'.$kind.'/recommendations'),
            $this,
        );
    }

    /**
     * A playlist's trailer, and the tracks it is built from.
     */
    public function usersPlaylistsTrailer(
        string|int $kind,
        string|int|null $userId = null,
    ): ?PlaylistTrailer {
        $userId ??= $this->accountUid();

        return PlaylistTrailer::fromApi(
            $this->request->get($this->getBaseUrl().'/users/'.$userId.'/playlists/'.$kind.'/trailer'),
            $this,
        );
    }

    /**
     * A playlist by its uuid rather than by owner and kind.
     *
     * The form a shared link carries, and the only one that works without
     * knowing whose playlist it is.
     */
    public function playlist(string $playlistUuid): ?Playlist
    {
        return Playlist::fromApi(
            $this->request->get($this->getBaseUrl().'/playlist/'.$playlistUuid),
            $this,
        );
    }

    /**
     * What to listen to next when a playlist runs out.
     */
    public function playlistSimilarEntities(string $playlistUuid): ?PlaylistSimilarEntities
    {
        return PlaylistSimilarEntities::fromApi(
            $this->request->get($this->getBaseUrl().'/playlist/'.$playlistUuid.'/similar-entities'),
            $this,
        );
    }

    /**
     * Fetch playlists by owner-and-kind pairs, as `{uid}:{kind}`.
     *
     * The query parameter is called `playlistIds`, which reads like it would
     * take the uuid a shared link carries. It does not: a uuid here is
     * refused with a validation error. Use playlist() for a uuid.
     *
     * @param string|list<string> $playlistIds
     */
    public function playlists(string|array $playlistIds): ?PlaylistsList
    {
        $result = $this->request->get($this->getBaseUrl().'/playlists', [
            'playlistIds' => is_array($playlistIds) ? implode(',', $playlistIds) : $playlistIds,
        ]);

        return PlaylistsList::fromApi($result, $this);
    }

    /**
     * Fetch playlists by owner-and-kind pairs, as `{uid}:{kind}`.
     *
     * The same input as playlists(), posted rather than queried, and answering
     * with a bare list rather than an envelope.
     *
     * @param string|list<string> $playlistIds
     *
     * @return list<Playlist>
     */
    public function playlistsList(string|array $playlistIds): array
    {
        $result = $this->request->post($this->getBaseUrl().'/playlists/list', [
            'playlist-ids' => is_array($playlistIds) ? implode(',', $playlistIds) : $playlistIds,
        ]);

        return Playlist::listFromApi($result, $this);
    }

    /**
     * One of the playlists the service generates for the account, such as the
     * daily playlist.
     */
    public function playlistsPersonal(string $playlistId): ?GeneratedPlaylist
    {
        return GeneratedPlaylist::fromApi(
            $this->request->get($this->getBaseUrl().'/playlists/personal/'.$playlistId),
            $this,
        );
    }

    /**
     * Join a collective playlist with an invitation token.
     *
     * Unlike every other write here, this one carries its arguments in the
     * query string rather than the body — the API rejects them otherwise.
     */
    public function playlistsCollectiveJoin(string|int $userId, string $token): bool
    {
        $url = $this->getBaseUrl().'/playlists/collective/join?'
            .http_build_query(['uid' => $userId, 'token' => $token]);

        return 'ok' === $this->request->post($url);
    }

    /**
     * Post a single-valued change and read the playlist back.
     */
    private function setPlaylistValue(
        string|int $kind,
        string $path,
        string $value,
        string|int|null $userId,
    ): ?Playlist {
        $userId ??= $this->accountUid();

        $result = $this->request->post(
            $this->getBaseUrl().'/users/'.$userId.'/playlists/'.$kind.'/'.$path,
            ['value' => $value],
        );

        return Playlist::fromApi($result, $this);
    }

    /**
     * The revision a playlist is currently at, for a change that did not
     * bring its own.
     */
    private function currentPlaylistRevision(string|int $kind, string|int $userId): int
    {
        $revision = $this->usersPlaylists($kind, $userId)?->revision;

        if (null === $revision) {
            throw new YandexMusicException(sprintf(
                'Could not read the current revision of playlist %s; pass one explicitly.',
                $kind,
            ));
        }

        return $revision;
    }
}
