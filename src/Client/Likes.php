<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Clip\ClipsWillLike;
use LuckyWins\YandexMusic\Model\Like;
use LuckyWins\YandexMusic\Model\TracksList;

/**
 * Likes and dislikes.
 *
 * Tracks are the odd ones out: their likes come back as a TracksList carrying
 * a revision, while albums, artists and playlists come back as lists of Like.
 * That is the API's shape, not ours.
 *
 * Every method acts on the authorized account unless given a $userId.
 */
trait Likes
{
    /**
     * The account's liked tracks.
     *
     * Pass the revision of a list you already have and the API answers with
     * nothing when it has not changed — which this reports as null. That is
     * what makes polling the library cheap.
     */
    public function usersLikesTracks(
        string|int|null $userId = null,
        int $ifModifiedSinceRevision = 0,
    ): ?TracksList {
        $userId ??= $this->accountUid();

        $result = $this->request->get($this->getBaseUrl().'/users/'.$userId.'/likes/tracks', [
            'if-modified-since-revision' => $ifModifiedSinceRevision,
        ]);

        return TracksList::fromApi(is_array($result) ? ($result['library'] ?? null) : null, $this);
    }

    /**
     * The account's liked albums.
     *
     * @param bool $rich ask for whole albums rather than stubs
     *
     * @return list<Like>
     */
    public function usersLikesAlbums(string|int|null $userId = null, bool $rich = true): array
    {
        return $this->likesOf('album', $userId, ['rich' => self::flag($rich)]);
    }

    /**
     * The account's liked artists.
     *
     * @param bool $withTimestamps ask when each artist was liked
     *
     * @return list<Like>
     */
    public function usersLikesArtists(string|int|null $userId = null, bool $withTimestamps = false): array
    {
        return $this->likesOf('artist', $userId, ['with-timestamps' => self::flag($withTimestamps)]);
    }

    /**
     * The account's liked playlists.
     *
     * @return list<Like>
     */
    public function usersLikesPlaylists(string|int|null $userId = null): array
    {
        return $this->likesOf('playlist', $userId);
    }

    /**
     * @param string|int|list<string|int> $trackIds
     */
    public function usersLikesTracksAdd(string|int|array $trackIds, string|int|null $userId = null): bool
    {
        return $this->likeAction('track', $trackIds, false, $userId);
    }

    /**
     * @param string|int|list<string|int> $trackIds
     */
    public function usersLikesTracksRemove(string|int|array $trackIds, string|int|null $userId = null): bool
    {
        return $this->likeAction('track', $trackIds, true, $userId);
    }

    /**
     * @param string|int|list<string|int> $albumIds
     */
    public function usersLikesAlbumsAdd(string|int|array $albumIds, string|int|null $userId = null): bool
    {
        return $this->likeAction('album', $albumIds, false, $userId);
    }

    /**
     * @param string|int|list<string|int> $albumIds
     */
    public function usersLikesAlbumsRemove(string|int|array $albumIds, string|int|null $userId = null): bool
    {
        return $this->likeAction('album', $albumIds, true, $userId);
    }

    /**
     * @param string|int|list<string|int> $artistIds
     */
    public function usersLikesArtistsAdd(string|int|array $artistIds, string|int|null $userId = null): bool
    {
        return $this->likeAction('artist', $artistIds, false, $userId);
    }

    /**
     * @param string|int|list<string|int> $artistIds
     */
    public function usersLikesArtistsRemove(string|int|array $artistIds, string|int|null $userId = null): bool
    {
        return $this->likeAction('artist', $artistIds, true, $userId);
    }

    /**
     * Like playlists, identified as `{uid}:{kind}`.
     *
     * @param string|list<string> $playlistIds
     */
    public function usersLikesPlaylistsAdd(string|array $playlistIds, string|int|null $userId = null): bool
    {
        return $this->likeAction('playlist', $playlistIds, false, $userId);
    }

    /**
     * @param string|list<string> $playlistIds
     */
    public function usersLikesPlaylistsRemove(string|array $playlistIds, string|int|null $userId = null): bool
    {
        return $this->likeAction('playlist', $playlistIds, true, $userId);
    }

    /**
     * The account's disliked tracks. Revisions work as they do for likes.
     */
    public function usersDislikesTracks(
        string|int|null $userId = null,
        int $ifModifiedSinceRevision = 0,
    ): ?TracksList {
        $userId ??= $this->accountUid();

        // The reference sends this one with underscores and the like variant
        // with hyphens; nothing else in either library uses underscores, so
        // that reads as a typo. Hyphens here, checked against the live API.
        $result = $this->request->get($this->getBaseUrl().'/users/'.$userId.'/dislikes/tracks', [
            'if-modified-since-revision' => $ifModifiedSinceRevision,
        ]);

        return TracksList::fromApi(is_array($result) ? ($result['library'] ?? null) : null, $this);
    }

    /**
     * The account's disliked artists.
     *
     * The reference hands these back as bare artists, which loses the time
     * each was disliked: the API sends that alongside the artist's own fields,
     * and an Artist has nowhere to put it. They come back as Like objects
     * instead, the same shape as usersLikesArtists().
     *
     * @return list<Like>
     */
    public function usersDislikesArtists(string|int|null $userId = null): array
    {
        $userId ??= $this->accountUid();

        $result = $this->request->get($this->getBaseUrl().'/users/'.$userId.'/dislikes/artists');

        return Like::listOfType($result, 'artist', $this);
    }

    /**
     * @param string|int|list<string|int> $trackIds
     */
    public function usersDislikesTracksAdd(string|int|array $trackIds, string|int|null $userId = null): bool
    {
        return $this->dislikeAction('track', $trackIds, false, $userId);
    }

    /**
     * @param string|int|list<string|int> $trackIds
     */
    public function usersDislikesTracksRemove(string|int|array $trackIds, string|int|null $userId = null): bool
    {
        return $this->dislikeAction('track', $trackIds, true, $userId);
    }

    /**
     * @param string|int|list<string|int> $artistIds
     */
    public function usersDislikesArtistsAdd(string|int|array $artistIds, string|int|null $userId = null): bool
    {
        return $this->dislikeAction('artist', $artistIds, false, $userId);
    }

    /**
     * @param string|int|list<string|int> $artistIds
     */
    public function usersDislikesArtistsRemove(string|int|array $artistIds, string|int|null $userId = null): bool
    {
        return $this->dislikeAction('artist', $artistIds, true, $userId);
    }

    /**
     * A page of the account's liked clips.
     */
    public function usersLikesClips(
        int $page = 0,
        int $pageSize = 20,
        string|int|null $userId = null,
    ): ?ClipsWillLike {
        $userId ??= $this->accountUid();

        $result = $this->request->get($this->getBaseUrl().'/users/'.$userId.'/likes/clips', [
            'page' => $page,
            'pageSize' => $pageSize,
        ]);

        return ClipsWillLike::fromApi($result, $this);
    }

    public function usersLikesClipsAdd(string|int $clipId, string|int|null $userId = null): bool
    {
        return $this->clipLikeAction('add', $clipId, $userId);
    }

    public function usersLikesClipsRemove(string|int $clipId, string|int|null $userId = null): bool
    {
        return $this->clipLikeAction('remove', $clipId, $userId);
    }

    /**
     * Fetch one kind of like and stamp the results with that kind.
     *
     * @param array<string, string> $params
     *
     * @return list<Like>
     */
    private function likesOf(string $objectType, string|int|null $userId, array $params = []): array
    {
        $userId ??= $this->accountUid();

        $result = $this->request->get(
            $this->getBaseUrl().'/users/'.$userId.'/likes/'.$objectType.'s',
            $params,
        );

        return Like::listOfType($result, $objectType, $this);
    }

    /**
     * @param string|int|list<string|int> $ids
     */
    private function likeAction(
        string $objectType,
        string|int|array $ids,
        bool $remove,
        string|int|null $userId,
    ): bool {
        return $this->markAction('likes', $objectType, $ids, $remove, $userId);
    }

    /**
     * @param string|int|list<string|int> $ids
     */
    private function dislikeAction(
        string $objectType,
        string|int|array $ids,
        bool $remove,
        string|int|null $userId,
    ): bool {
        return $this->markAction('dislikes', $objectType, $ids, $remove, $userId);
    }

    /**
     * Likes and dislikes are the same request against a different path.
     *
     * Track operations answer with the library's new revision rather than
     * `ok`, so success looks different for them. The revision itself is not
     * returned: it is on the TracksList the listing methods hand back, and
     * having all eight of these agree on a type is worth more.
     *
     * @param string|int|list<string|int> $ids
     */
    private function markAction(
        string $mark,
        string $objectType,
        string|int|array $ids,
        bool $remove,
        string|int|null $userId,
    ): bool {
        $userId ??= $this->accountUid();
        $action = $remove ? 'remove' : 'add-multiple';

        $result = $this->request->post(
            $this->getBaseUrl().'/users/'.$userId.'/'.$mark.'/'.$objectType.'s/'.$action,
            [$objectType.'-ids' => is_array($ids) ? implode(',', $ids) : $ids],
        );

        if ('track' === $objectType) {
            return is_array($result) && array_key_exists('revision', $result);
        }

        return 'ok' === $result;
    }

    /**
     * Clip likes take their argument in the query string, not the body.
     */
    private function clipLikeAction(string $action, string|int $clipId, string|int|null $userId): bool
    {
        $userId ??= $this->accountUid();

        $url = $this->getBaseUrl().'/users/'.$userId.'/likes/clips/'.$action.'?'
            .http_build_query(['clip-id' => $clipId]);

        $result = $this->request->post($url);

        return 'ok' === $result || is_array($result);
    }

    /**
     * Booleans go on the wire capitalized, as the reference sends them.
     */
    private static function flag(bool $value): string
    {
        return $value ? 'True' : 'False';
    }
}
