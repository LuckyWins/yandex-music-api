# Albums and artists

Ported in the fourth stage. Ten new models, eleven client methods, twelve
fields added to models that already existed.

## Why this stage was small

The previous stage ported `Track`, `Album` and `Artist` with their whole
subtree, because the three are one strongly connected component and cannot be
separated. What it did not port was their endpoints: albums and artists still
went through `Legacy` and came back as raw arrays.

So this stage is mostly the client half. It was chosen less for its size than
as a test of the previous one: those models were written against responses
where an album or an artist is a nested object, and it was an open question
whether they held up where it is the subject of the response.

They did. `/albums/{id}/with-tracks` deserializes into the existing `Album`
without a single change to it — discs, their tracks, and the artists and albums
nested inside those.

## Methods

| Was (`Client\Legacy`) | Became | In Python | Difference from the reference |
|---|---|---|---|
| `albums(ids): mixed` | `albums(ids): list<Album>` | `albums(album_ids)` | none |
| `albumsWithTracks(id): array` | `albumsWithTracks(id): ?Album` | `albums_with_tracks()` | none |
| `artists(ids): mixed` | `artists(ids): list<Artist>` | `artists(artist_ids)` | none |
| `artistsBriefInfo(id): array` | `artistsBriefInfo(id): ?BriefInfo` | `artists_brief_info()` | theirs is fully typed; ours leaves concerts, clips, vinyls and playlists as raw arrays until those domains are ported |
| — | `album(id): ?Album` | no such method | ours: the endpoint exists and answers; the reference only offers the with-tracks variant |
| — | `albumsDisclaimer(id): list<Disclaimer>` | `albums_disclaimer()` | theirs declares one object, the API sends a list |
| — | `artistsTracks(id, page, pageSize): ?ArtistTracks` | `artists_tracks()` | none |
| — | `artistsDirectAlbums(id, page, pageSize, sortBy): ?ArtistAlbums` | `artists_direct_albums()` | none |
| — | `artistsAlsoAlbums(id, page, pageSize, sortBy): ?ArtistAlbums` | `artists_also_albums()` | none |
| — | `artistsTrackIdsByRating(id): list<int>` | `artists_track_ids_by_rating()` | none |
| — | `artistsSimilar(id): ?SimilarArtists` | no such method | ours: the endpoint answers directly; the reference can only reach similar artists through brief-info |
| `tracksDisclaimer(id): ?Disclaimer` | `tracksDisclaimer(id): list<Disclaimer>` | `tracks_disclaimer()` | fixes our own bug from the previous stage — see below |

Batch `albums()` and `artists()` no longer go through `Legacy::getList()`. That
helper stays for the playlists, which have not had their turn.

## The disclaimer bug

`/tracks/{id}/disclaimer` was modelled last stage as returning one object,
copying the reference. It returns a list. The same is true of the album
endpoint, checked against the live API on both.

On every object checked the list was empty, so the shape of an element is still
unknown; `listFromApi()` handles an empty list correctly and will deserialize
elements when one finally arrives. Both methods now return `list<Disclaimer>`.

## Fields the models were missing

Found by running every endpoint in this stage with unknown-field reporting on.
The shapes below were read off live responses, not guessed.

`Album` was missing eight: `cover`, `derivedColors`, `trailer`, `hasTrailer`,
`customWave`, `pager`, `metaTagId`, `sortOrder`. `cover` and `coverUri` are the
same art in two forms and arrive independently of each other, which a test
pins.

`Artist` was missing four: `derivedColors`, `trailer`, `hasTrailer` and
`extraActions`. The last is a list, empty on everything checked, so it stays
raw until something non-empty turns up.

`Track` was missing `chart`. It never appears in this domain's responses — it
comes from the landing — but the model now declares it, so the landing stage
will not have to.

A second pass with reporting on is clean: no endpoint in this domain sends a
field no model declares.

## Models — ten new

`Pager`, `Trailer`, `CustomWave` and `Artist\Stats` come from the missing
fields above. `Landing\TrackId` and `Landing\Chart` come with `Track::$chart`.

`Artist\ArtistTracks`, `Artist\ArtistAlbums` and `Artist\SimilarArtists` are
response envelopes: a pager or an artist, plus a list.

`Artist\BriefInfo` is the one deliberate compromise. The response is a wrapper
of 22 keys, and among them are concerts, clips, vinyls and playlists — whole
domains this library does not have yet. What there are models for is typed:
the artist, its albums and also-albums, last releases, popular tracks, similar
artists, covers, videos, chart entries, stats and custom wave. The rest stays
as raw arrays, each with a comment naming the domain it is waiting for. Pulling
four domains forward out of order to satisfy one method would have cost more
than it bought.

## Verified against the live API

Every method in the table was called against the real API, not only mocked.

- `artistsTracks(4611844, 1, 3)` returns page 1 with three tracks and a pager
  reporting 88 in total — pagination reaches the request and comes back in the
  response.
- `artistsBriefInfo` deserializes into 9 albums, 10 popular tracks, 10 similar
  artists, 8 covers, 2 videos, and stats reporting 8,659,896 listeners.
- `albumsWithTracks` returns discs with their tracks, each track carrying its
  own artists and album.

## Left for later

Playlists, likes and dislikes, radio, the landing, and search. Concerts, clips
and vinyls as domains of their own — `BriefInfo` will type them when they land.
