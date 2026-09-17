# Playlists

Ported in the fifth stage. Twenty-one models, twenty client methods, one
request builder.

## Why playlists came before the rest

Four domains were left after albums and artists: likes, radio, the landing and
search. Three of the four embed a whole playlist — a liked playlist, a landing
block of playlists, a playlist in a search result — so each of them would have
typed only halfway without this one. Radio was the alternative and unblocks
nothing.

The gap with the reference was also the widest here: seven methods against
twenty. Collective playlists, recommendations, trailers, descriptions,
visibility, personal playlists and similar entities had no equivalent at all.

## Methods

| Was (`Client\Legacy`) | Became (`Client\Playlists`) | In Python | Difference from the reference |
|---|---|---|---|
| `usersPlaylists(kind, userId): mixed` | `usersPlaylists(kind, userId): ?Playlist` | `users_playlists()` | theirs returns a playlist or a list depending on the argument; ours is split in two, each with an honest type |
| — | `usersPlaylistsMany(kinds, userId): list<Playlist>` | same `users_playlists()` | ours: the other half of their overload |
| `usersPlaylistsList(): array` | `usersPlaylistsList(userId): list<Playlist>` | `users_playlists_list()` | none |
| `usersPlaylistsCreate(title, visibility): array` | `usersPlaylistsCreate(title, visibility, userId): ?Playlist` | `users_playlists_create()` | none |
| `usersPlaylistsDelete(kind): mixed` | `usersPlaylistsDelete(kind, userId): bool` | `users_playlists_delete()` | none |
| `usersPlaylistsNameChange(kind, name): mixed` | `usersPlaylistsName(kind, name, userId): ?Playlist` | `users_playlists_name()` | renamed to match the reference |
| `usersPlaylistsInsertTrack(kind, trackId, albumId, at, revision): mixed` | `usersPlaylistsInsertTrack(kind, TrackId, at, revision, userId): ?Playlist` | `users_playlists_insert_track()` | takes a `TrackId` rather than a pair of loose numbers |
| `playlistsList(ids): mixed` | `playlistsList(ids): list<Playlist>` | `playlists_list()` | none |
| private `usersPlaylistsChange(kind, string, revision)` | `usersPlaylistsChange(kind, PlaylistDiff, revision, userId): ?Playlist` | `users_playlists_change()` | public, and takes a typed diff instead of a JSON string |
| — | `usersPlaylistsDeleteTrack(kind, from, to, revision, userId): ?Playlist` | `users_playlists_delete_track()` | none |
| — | `usersPlaylistsVisibility(kind, visibility, userId): ?Playlist` | `users_playlists_visibility()` | none |
| — | `usersPlaylistsDescription(kind, description, userId): ?Playlist` | `users_playlists_description()` | none |
| — | `usersPlaylistsKinds(userId): list<int>` | `users_playlists_kinds()` | none |
| — | `usersPlaylistsRecommendations(kind, userId): ?PlaylistRecommendations` | `users_playlists_recommendations()` | none |
| — | `usersPlaylistsTrailer(kind, userId): ?PlaylistTrailer` | `users_playlists_trailer()` | none |
| — | `playlist(uuid): ?Playlist` | `playlist()` | none |
| — | `playlistSimilarEntities(uuid): ?PlaylistSimilarEntities` | `playlist_similar_entities()` | none |
| — | `playlists(ids): ?PlaylistsList` | `playlists()` | none |
| — | `playlistsPersonal(id): ?GeneratedPlaylist` | `playlists_personal()` | none |
| — | `playlistsCollectiveJoin(userId, token): bool` | `playlists_collective_join()` | none |
| — | `usersSettings(userId): ?UserSettings` | `users_settings()` | theirs sits among the playlist methods; ours is in the account trait, where it belongs — it reads account state and returns the model the account domain already had |

`Legacy` is down to 29 methods: likes, radio, the landing and search.

## Changes as a value object

The reference builds a playlist change with a `Difference` class that produces
a JSON string, and `users_playlists_change` takes that string. Here the
operations stay structured until the request is made:

```php
$diff = (new PlaylistDiff())
    ->insert(0, new TrackId(id: 31190260, albumId: 4243617))
    ->delete(3, 5);
```

`PlaylistDiff` is not a model — nothing deserializes into it, it only travels
outward. It refuses what the API would refuse anyway: an insert without an
album id, an insert with no tracks, a descending range, and an empty change.
Catching those here rather than at the server is the whole reason it is typed.

`usersPlaylistsInsertTrack()` and `usersPlaylistsDeleteTrack()` are thin
wrappers over it, as in the reference.

### Revisions

Every change carries the revision it was built against, and the API rejects a
stale one rather than losing an edit. Passing one is the good path. Omitting it
makes the client read the current revision first, which costs a request and
still races with anyone else editing — the reference has the same behavior and
the same caveat.

## Models

`Model/Playlist/` gained fourteen: `Playlist` itself, `CaseForms`, `MadeFor`,
`PlayCounter`, `PlaylistAbsence`, `PlaylistAvailability`, `Brand`, `Contest`,
`OpenGraphData`, `GeneratedPlaylist`, and the response envelopes
`PlaylistsList`, `PlaylistRecommendations`, `PlaylistSimilarEntities`,
`PlaylistTrailer`.

`Model/Track/TrackShort` is a track's place in a playlist rather than the track:
its id, when it was added, where it sits. A playlist of two hundred tracks
arrives as two hundred of these, and the tracks themselves are fetched with
`tracks()` when they are wanted.

`Model/TrailerInfo` holds what a playlist trailer plays.

`Model/Wave/` is five small models — `Wave`, `WaveAgent`, `WaveAgentEntity`,
`SimilarEntityData`, `SimilarEntityItem` — pulled in by `similar-entities`.
Two to four fields each; typing them cost less than leaving them raw and
coming back.

`Model/Playlist/User` already existed, written two stages ago as the uploader
of a track. It is the playlist owner unchanged.

Four fields of `Playlist` stay raw: `tags`, `prerolls`, `regions` and
`isForFrom`. The reference does not type them either, and nothing seen so far
pins their shape. `PlaylistId`, `Tag` and `TagResult` belong to the landing and
were not needed by any method here.

## Fields the reference does not have

Running the probe with unknown-field reporting on turned up three things the
reference library does not model, all of them read off live responses rather
than guessed:

- **`Playlist::$madeForUser`** — `{isMadeForUser, caseForms}`. Close to
  `madeFor` and not the same: a flag instead of the user. It became
  `MadeForUser`, which has no counterpart in the reference.
- **`Playlist::$derivedColors`** — the same four colors as everywhere else, so
  it reuses the existing `CoverDerivedColors`.
- **`CustomWave::$position` and `CustomWave::$squareAgentAnimation`** — both
  strings, found on the account's own playlists. `CustomWave` was added two
  stages ago from album and artist responses, which do not carry these.

After adding them a second run reports nothing unknown across the domain.

## Where the uid lives now

`accountUid()` was a private helper inside `Legacy`. Playlists need it, likes
still need it, and `Legacy` is supposed to disappear — so it moved to the
`Account` trait, next to the state it reads. Nothing else changed about it,
including the message it throws when `init()` was never called.

## What the live run corrected

Two things were wrong before the probe ran, and both were mine rather than the
reference's:

- `playlists()` takes owner-and-kind pairs, not the uuid a shared link carries,
  despite its parameter being named `playlistIds`. A uuid there is refused with
  a validation error. `playlist()` is the one that takes a uuid.
- `similar-entities` answers `playlist-not-found` for a private playlist even
  when you own it, and answers normally once the playlist is public.

Two more results are the API working as intended rather than problems: a
playlist created a moment ago has no trailer, and the endpoint says so with a
404; and recommendations for an empty playlist are empty.

## The one endpoint that is not like the others

`/playlists/collective/join` is a POST whose arguments the API only accepts in
the query string. Rather than grow `Request::post()` a parameters argument for
one caller, the trait builds the query itself.

## Verifying the writes

Ten of these methods change data, and mocked tests cannot tell whether the API
still accepts what we send. `examples/playlist_roundtrip.php` creates a
playlist named `api-probe <timestamp>` and then exercises the whole domain
against it: rename, hide, describe, insert a track, remove it, recommendations,
trailer, then every reading method — by kind, by kinds, by owner-and-kind pair,
by uuid, in a batch, its similar entities, and the account's playlist of the
day. It finishes by deleting the playlist and printing the playlist count
before and after.

It refuses to run without confirmation, the same as `revoke_token.php`, and a
failure partway through still falls through to the delete, so a bad run leaves
nothing behind.
