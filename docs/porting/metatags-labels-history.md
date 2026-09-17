# Metatags, labels and the listening history

The thirteenth stage, and the one that closes the gap. Twenty-one models, ten
methods, three domains that were never in the 2019 library.

**Nothing the Python library has is missing here any more** — and that is now
checked by a script rather than remembered.

## Methods

Three new traits: `Client\Metatags`, `Client\Labels`, `Client\MusicHistory`.

| Was | Became | In Python | Difference from the reference |
|---|---|---|---|
| — | `metatags(): ?Metatags` | `metatags()` | none |
| — | `metatag(id, …counts, …sortBy, withLikesCount): ?Metatag` | `metatag()` | none |
| — | `metatagAlbums(id, offset, limit, period, sortBy): ?MetatagAlbums` | `metatag_albums()` | none |
| — | `metatagArtists(id, period, offset, limit, sortBy, tracksPerArtist): ?MetatagArtists` | `metatag_artists()` | none |
| — | `metatagPlaylists(id, offset, limit, sortBy): ?MetatagPlaylists` | `metatag_playlists()` | none |
| — | `label(id): ?Label` | `label()` | none |
| — | `labelAlbums(id, page, pageSize, sortBy, sortOrder): ?LabelAlbums` | `label_albums()` | none |
| — | `labelArtists(id, page, pageSize): ?LabelArtists` | `label_artists()` | none |
| — | `musicHistory(fullModelsCount): ?MusicHistory` | `music_history()` | none |
| — | `musicHistoryItems(MusicHistoryQuery): ?MusicHistoryItems` | `music_history_items()` | takes a query object rather than five parallel lists, two of them lists of pairs |

## A tag is named in Russian

A metatag's id is not a slug: it is `Осенняя музыка`, `Музыка, чтобы
проснуться`, `Бег`. Pasted into a path unencoded, the service answers
`not found` with the name mangled in the message — which reads as though the
tag is wrong rather than the request. The ids are encoded now, and a test
pins it.

Worth knowing beyond this library: an id from this API is not necessarily
URL-safe.

## A query object instead of five lists

`music_history_items` takes five parallel lists in the reference, two of which
are lists of pairs. PHP has no tuples, and a parameter typed
`list<array{0: string|int, 1: string|int}>` says nothing to a reader. So it
takes a builder, the same shape as `PlaylistDiff` from the playlists stage:

```php
$client->musicHistoryItems(
    (new MusicHistoryQuery())
        ->track(31190260, 4243617)
        ->album(4243617)
        ->playlist(503646255, 1042),
);
```

The body sent is the same. An empty query is refused rather than sent.

## What a history entry holds

`MusicHistoryItem::$data->fullModel` is a `Track` for a track and a
`MusicHistoryContextFullModel` for everything else — an album, an artist, a
playlist or a station. Only the entry's own type says which, so the model is
built in `prepare()`, the pattern used since the search stage.

Entries can also arrive as references alone, without the thing itself: that is
what `fullModelsCount` limits, and `musicHistoryItems()` is how the rest get
filled in. The live run asked about three tracks and got three back.

## Four fields left raw

`Metatag` is sent with `tracks`, `composers`, `promotions` and `features`, and
all four have been empty in every response seen. The reference says the same in
its own docstring and does not model them. They are declared and left raw,
with a comment saying why, rather than typed on a guess.

## Parity, checked rather than remembered

`tools/compare-with-reference.php` reads the `def` names out of the reference's
`_client/` and compares them against this `Client`'s public methods, printing
what is missing in either direction. It is a script and not a test because CI
has no checkout of the reference; without one it says so and exits quietly.

Its answer today:

```
reference: 144 methods
here:      159 methods

Nothing the reference has is missing here.
```

The fifteen extra are accessors (`getToken`, `getBaseUrl`, `getDevice`) and
conveniences this library added: `album()`, `artistsTrackIdsByRating()`,
`accountSettingsSetMany()`, `revokeToken()`, `me()`, and the unknown-field
reporting.

## The probe

`examples/tags_labels_history.php` reads only — its one POST asks the history
to fill entries in, which changes nothing — so it runs without confirmation.
It walks the tag trees, opens one tag with its albums, artists and playlists,
finds a label through an album and lists its releases and artists, prints the
history by day, and asks for a few entries by reference.
