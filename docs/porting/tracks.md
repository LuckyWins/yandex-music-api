# Tracks, albums and artists

Ported in the third stage. 35 models, eleven client methods.

## Why these three together

`search` was meant to come next, but its results embed whole objects rather
than stubs — checked against the live API, one track in a search response
carries full artists and albums, six levels deep. Typing search means typing
those first.

Measuring the dependency closures settled the order:

```
Track(23) == Album(23) == Artist(23)  ⊂  Playlist(37)  ⊂  search(42)
```

Track, Album and Artist are one strongly connected component — their closures
are identical, class for class, because they reference one another. There is no
way to port one alone. Playlist adds 14 more on top; search only 5 beyond that.

## Methods

| Was (`Client\Legacy`) | Became (`Client\Tracks`) | In Python | Difference from the reference |
|---|---|---|---|
| `tracks(ids): mixed` | `tracks(ids, bool): list<Track>` | `tracks(track_ids, with_positions)` | none |
| `tracksDownloadInfo(id): mixed` | `tracksDownloadInfo(id): list<DownloadInfo>` | `tracks_download_info(track_id, get_direct_links)` | theirs takes a flag that resolves every direct link in a serial loop, against manifests that expire in a minute. Ours resolves one at a time, when asked |
| `getDirectLink(): never` | `DownloadInfo::directLink()` | `DownloadInfo.get_direct_link()` | moved off the client onto the model that holds the manifest |
| — | `DownloadInfo::download(path): int` | `DownloadInfo.download(filename)` | streams in 64 KiB chunks; theirs buffers the whole file in memory |
| — | `trackSupplement(id): ?Supplement` | `track_supplement()` | none |
| — | `tracksLyrics(id, format): ?TrackLyrics` | `tracks_lyrics()` | none |
| — | `tracksSimilar(id): ?SimilarTracks` | `tracks_similar()` | none |
| — | `tracksTrailer(id): ?TrackTrailer` | `tracks_trailer()` | none |
| — | `tracksFullInfo(id): ?TrackFullInfo` | `tracks_full_info()` | none |
| — | `playAudio(...): bool` | `play_audio()` | theirs stamps local time and labels it `Z`, which is wrong anywhere but on a UTC machine. Ours sends real UTC |
| — | `afterTrack(...): ?ShotEvent` | `after_track()` | none |
| — | `tracksCredits(id): ?Credits` | `tracks_credits()` | theirs lives in a credits domain; ours sits with the other track endpoints |
| — | `tracksDisclaimer(id): ?Disclaimer` | `tracks_disclaimer()` | same |
| `albumsWithTracks(id): array` | unchanged, still in `Legacy` | `albums_with_tracks()` | the albums domain, not this stage — though the `Album` model now exists |

Likes and dislikes stay in `Legacy`: they belong to the likes domain.

## Download

Checked against the live API rather than read off the source.

`/tracks/{id}/download-info` still answers, still returns manifests, and the
manifest XML still carries `host`, `path`, `ts` and `s` — the same fields as in
2019. The host has changed: it is `api.music.yandex.net` now, where it used to
be a storage host.

**The old md5-and-salt scheme still works.** Verified end to end by fetching a
real track: 10.4 MiB of MPEG layer III, 320 kbps, 44.1 kHz, its duration
matching the value on the model. The stage-one conclusion that the scheme was
dead — and the exception `getDirectLink()` threw as a result — was simply
wrong.

What had made it look dead was our own HTTP layer. Audio is served by redirect,
and a PSR-18 client does not follow redirects: it hands back the 3xx so the
caller can decide. Correct for API calls, fatal for files. Fetching a file now
follows up to five hops; API calls still treat a redirect as the anomaly it
would be.

The URL construction is pinned by a test against a fixed manifest, so a change
to the algorithm fails there rather than producing audio that will not play.

Two caveats that matter in practice:

- **A manifest is good for about a minute.** Resolve it and fetch promptly;
  holding a list of them and resolving later gets you nothing.
- Downloads stream rather than buffer. An album is not something to hold in
  memory.

The HMAC-SHA256 signature in the reference has nothing to do with downloads —
it signs lyrics requests only. That is verified: a request signed this way
returns a lyrics record, and an unsigned or wrongly signed one is refused
outright rather than answered.

## What this stage changed in the model base

Three fields cannot be declared through `NESTED`, because their shape is
decided by content rather than by key. Rather than have each override `fromApi()`
and rebuild the whole model by hand — one misplaced argument among fifty would
corrupt data silently — the base gained a `prepare()` hook that adjusts the
collected arguments just before construction.

- **`Album::$labels`** — objects from one endpoint, bare names from another
- **`Album::$volumes`** — one list of tracks per disc
- **`Artist::$decomposed`** — a credit line alternating artists with the words
  joining them

## Cycles

Track, Album and Artist reference one another, and four fields point at their
own class. The schema is cyclic; the data is not, because the API truncates
what it embeds.

Nothing guards against recursion, and nothing needs to: the resolver descends
only where a key is actually present, and an absent one resolves to null
immediately. The reference relies on the same property, but by accident — it
has no guard either, and its test suite has to hand-build stripped-down
fixtures to terminate. There is a test here that walks a cyclic payload five
levels down and asserts it stops where the data does.

## Left for later

The albums, artists and playlists domains as sets of client methods — their
models exist now, their endpoints do not. Then `search`, which those unlock.
