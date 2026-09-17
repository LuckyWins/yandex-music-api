# Search

Ported in the seventh stage. Five models, one enum, two methods.

## Why search was cheap this time

Search was the first domain considered for porting and the first rejected: its
results embed whole objects rather than stubs, and typing it then would have
meant typing forty-odd models first. Six stages later those models exist, and
what was left is the wrapping: the result sets, the best match, the
suggestions, and `Video`.

## Methods

| Was (`Client\Legacy`) | Became (`Client\Search`) | In Python | Difference from the reference |
|---|---|---|---|
| `search(text, noCorrect, type, page, playlistInBest): array` | `search(text, noCorrect, SearchType, page, playlistInBest): ?Search` | `search()` | the type is an enum rather than a string, and result sets are read by the type the response declares rather than by the field they arrived in |
| `searchSuggest(part): array` | `searchSuggest(part): ?Suggestions` | `search_suggest()` | none |

`Legacy` is down to 12 methods: the landing with its feed and genres, and
radio.

## A generic without generics

The reference declares `SearchResult[Track]`, `SearchResult[Album]` and so on.
PHP has no generics, but PHPStan reads them from PHPDoc, and this project runs
at level 9 in CI — so `SearchResult` is one class with `@template T of Model`,
and `$search->tracks->results[0]` is known to be a `Track` everywhere it is
used. Six near-identical classes would have bought the same strictness for six
times the code.

What a generic cannot do is deserialize itself: `fromApi()` has no way to know
`T`. So result sets are built through a named factory, the same shape as
`Like::listOfType()`:

```php
SearchResult::of(Track::class, 'track', $data, $client);
```

## Reading a set by its own type

The reference carries a comment about the service occasionally filing playlists
under `artists`, or playlists under `users` — roughly ten responses in three
months of a production client. It only documents this; it reads every set by
the field name regardless.

We cannot afford that: an `Artist` built from a playlist is missing required
fields, which throws, and one misfiled set would take the whole search down.
So a set is built from the type the response declares, and the field name is
the fallback for when there is none. `SearchResult::$type` always says what was
actually built.

The fallback is not hypothetical. **The clips set arrives with no type at all**
— checked against the live API — so it is the one set whose contents are
decided by the field name.

## Models

`Search\Search`, `Search\SearchResult`, `Search\Best`, `Search\Suggestions` and
`Video`.

`Video` is not our `Supplement\VideoSupplement`: that one says how to embed a
player for a track's video, this one describes a video found by searching. The
reference keeps them apart too.

Result sets reuse what the earlier stages built: tracks and podcast episodes
are `Track`, albums and podcasts are `Album`, and the rest map to `Artist`,
`Playlist`, `Playlist\User`, `Clip` and `Video`.

`Best` is the one model that decides what to build from the payload rather than
from the field, through the `prepare()` hook the base class already had.

## The first enum

`SearchType` replaces the bare string. A misspelled type was answered with an
empty result rather than an error, which is the sort of bug that survives a
long time. The enum also carries the model each type deserializes into, which
is what both `Best` and `SearchResult` consult.

Radio and the landing have more of these strings; this is the pattern they
should follow.

## Fields the live run found

Search sends a `clips` result set, which the reference does not model at all,
and podcast episodes carry two fields `Track` did not declare:
`podcastEpisodeType` (`full` or `trailer`) and `pubDate` (`YYYY-MM-DD`). All
three were read off live responses. A second run reports nothing unknown.

## The probe

`examples/search.php` reads only, so it runs without asking for confirmation.
It searches three ways, prints each result set with the type it declared, marks
any set whose type disagrees with its field, fetches suggestions, and prints
the unknown-field summary. Pass a query of your own as the first argument.
