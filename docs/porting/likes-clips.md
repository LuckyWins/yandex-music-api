# Likes, dislikes and clips

Ported in the sixth stage. Five models, twenty-three methods, one model moved
out of the album namespace.

## Why these together

Likes were half of what `Legacy` still held, and they were cheap: everything
they return — albums, artists, playlists, track positions — was typed by the
earlier stages. Only `Like` and `TracksList` were missing.

Clips came along because liking a clip needs `Clip` and `ClipsWillLike`
anyway, and the clips domain proper is two more methods on those same two
models. Closing it cost nothing extra.

## Methods

| Was (`Client\Legacy`) | Became | In Python | Difference from the reference |
|---|---|---|---|
| `getLikesTracks(): mixed` | `usersLikesTracks(userId, ifModifiedSinceRevision): ?TracksList` | `users_likes_tracks()` | none |
| `getLikesAlbums(): mixed` | `usersLikesAlbums(userId, rich): list<Like>` | `users_likes_albums()` | none |
| `getLikesArtists(): mixed` | `usersLikesArtists(userId, withTimestamps): list<Like>` | `users_likes_artists()` | none |
| `getLikesPlaylists(): mixed` | `usersLikesPlaylists(userId): list<Like>` | `users_likes_playlists()` | none |
| `usersLikesTracksAdd/Remove(ids): mixed` | same, `bool`, plus `userId` | `users_likes_tracks_add/remove()` | ours returned the library's new revision; now `bool` like the reference, and the revision comes from `TracksList` |
| `usersLikesAlbumsAdd/Remove(ids): mixed` | same, `bool`, plus `userId` | `users_likes_albums_add/remove()` | none |
| `usersLikesArtistsAdd/Remove(ids): mixed` | same, `bool`, plus `userId` | `users_likes_artists_add/remove()` | none |
| `usersLikesPlaylistsAdd/Remove(ids): mixed` | same, `bool`, plus `userId` | `users_likes_playlists_add/remove()` | none |
| `usersDislikesTracks(revision): mixed` | `usersDislikesTracks(userId, ifModifiedSinceRevision): ?TracksList` | `users_dislikes_tracks()` | we send `if-modified-since-revision`, hyphenated, where theirs sends underscores — see below |
| `usersDislikesTracksAdd/Remove(ids): mixed` | same, `bool`, plus `userId` | `users_dislikes_tracks_add/remove()` | none |
| — | `usersDislikesArtists(userId): list<Like>` | `users_dislikes_artists()` | theirs returns bare artists and drops the time each was disliked; ours keeps it in a `Like`, the same shape the likes use |
| — | `usersDislikesArtistsAdd/Remove(ids, userId): bool` | `users_dislikes_artists_add/remove()` | none |
| — | `usersLikesClips(page, pageSize, userId): ?ClipsWillLike` | `users_likes_clips()` | none |
| — | `usersLikesClipsAdd/Remove(clipId, userId): bool` | `users_likes_clips_add/remove()` | none |
| — | `clips(ids): list<Clip>` | `clips()` | none |
| — | `clipsWillLike(page, pageSize): ?ClipsWillLike` | `clips_will_like()` | none |

`Legacy` is down to 14 methods: the landing with its feed and genres, search,
and radio.

## The type a like does not carry

A response full of likes says nothing about what kind of thing it holds, and
two shapes arrive:

- albums and playlists come wrapped, under a key named after their type;
- artists come bare — the response *is* the artist, with the time it was liked
  sitting among the artist's own fields.

The reference solves this by giving `de_json` a third argument. Changing the
shared deserializer for one model was not worth it, so `Like` has a named
factory instead:

```php
Like::listOfType($result, 'album', $client);
```

It stamps the type, lifts the timestamp out of the bare form so it lands on the
like rather than being reported as a field `Artist` does not know, and hands
the rest to the usual machinery.

## Revisions

`usersLikesTracks()` and `usersDislikesTracks()` take a revision and answer
with nothing when the library has not changed since — reported here as null.
That is what makes polling cheap.

The reference sends this parameter hyphenated for likes and underscored for
dislikes. Nothing else in either library uses underscores, so it reads as a
typo; we send the hyphenated form in both. Checked against the live API: asking
for the current revision returns nothing on both endpoints, so the filter
works.

## ActionButton moved

`Album\AlbumActionButton` is now `Model\ActionButton`. The live run showed a
playlist carrying the same `{text, url, color}` object, and a shared shape does
not belong inside one domain's namespace.

## Fields the live run found

Unknown-field reporting turned up five more, all read off live responses:

- `TracksList::$playlistUuid` — the library is itself a playlist
- `Album::$childContent` — boolean
- `CustomWave::$imageUrl` — a still image beside the animation
- `Playlist::$actionButton` — the model above
- a `timestamp` beside a bare liked or disliked artist, which now lands on the
  `Like`

A second run reports nothing unknown.

## What the live run corrected

The probe first reported that liking an album did nothing: the API answered
`ok` and the album never appeared in the library. The method was fine — the
album id written into the probe no longer exists, and `albums()` returns an
empty stub for it rather than an error. An id taken from the artist's own
listing round-trips.

Liking a playlist the account already owns behaves the same way: `ok`, and
nothing in the library. The probe now takes an editorial playlist from the
landing instead.

Both are worth remembering: **this API accepts a like for something that does
not exist, and says `ok`.** A write that reports success is not evidence the
object was real.

## The probe

`examples/likes_roundtrip.php` reads what is already liked, skips it, and marks
only objects it can unmark afterwards — a track, an album from the artist
listing, an artist, an editorial playlist, plus the two dislikes. Each mark is
followed by a listing read that retries for a few seconds, because a listing
can lag a write. It finishes by comparing the counts with the ones it started
from and printing the unknown-field summary.
