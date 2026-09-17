# Concerts

The twelfth stage. Sixteen models, six methods. The gap with the Python library
is down from 16 methods to 10.

This domain was deferred twice, and for one reason both times: `artistsConcerts`
and `BriefInfo::$concerts` answer with concerts, and pulling sixteen models in
for one field was not worth it. Its turn came, and both debts are settled here.

The skeleton models written two stages ago pay off for the first time:
`concertSkeleton` uses the same five without a change.

## Methods

Five in a new `Client\Concerts`; `artistsConcerts` goes to `Client\Artists`,
because its path is an artist's.

| Was | Became | In Python | Difference from the reference |
|---|---|---|---|
| — | `artistsConcerts(id): ?ArtistConcerts` | `artists_concerts()` | none |
| — | `concertInfo(uuid): ?ConcertInfo` | `concert_info()` | none |
| — | `concertSkeleton(uuid, skeletonId): ?ConcertSkeleton` | `concert_skeleton()` | none |
| — | `concertsFeed(?locations): ?ConcertFeed` | `concerts_feed()` | none |
| — | `concertsLocations(): ?ConcertLocations` | `concerts_locations()` | none |
| — | `concertsTabConfig(): ?ConcertTabConfig` | `concerts_tab_config()` | none |

## The suffix, again

The listing's entries are of type **`concert_item`**, not `concert` — the same
`_item` suffix a pinned album carries. The first live run reported 48 concerts
in the feed and zero after deserialization, because the type map had only the
unsuffixed spelling.

That is twice now in two stages: the service names a kind one way in the path
you create it with and another way in the response. Both spellings are accepted
here, in `ConcertFeedItem` as in `PinsList::ofType()`.

A listing that quietly comes back empty is a bad way to learn this, which is
why the probe prints the kinds it could not place rather than only the
concerts it could.

## What the live run showed

- 181 cities, and `concertsLocations()` maps a name to the id the feed filters
  by — `Москва` is 213.
- The feed answers 48 concerts for Moscow, with prices, venues and dates.
- `concertInfo()` and `concertSkeleton()` both answer for a concert taken from
  the feed; the skeleton is a single `TABS` block.
- The tab configuration says `top` is the first five and `feed` is
  `limit: -1` — everything after them.
- `artistsConcerts()` answers for an artist with nothing coming up: a title and
  an empty list, not an error.

## A debt half-settled

`BriefInfo::$concerts` stays raw. The domain is ported now, so the old comment
— "the concerts domain is not ported" — was no longer true, but the field has
only ever been seen empty, and the reference does not type it either. It is
left raw with a comment that says that, rather than typed on a guess.

## The probe

`examples/concerts.php` reads only, so it runs without confirmation: cities,
the tab configuration, the listing with and without a city filter, an artist's
dates, and one concert's page and layout. Pass an artist id and a city name as
arguments. It also prints what brief-info puts in its concerts field, which is
how the decision above was made.
