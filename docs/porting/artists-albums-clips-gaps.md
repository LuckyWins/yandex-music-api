# Filling the gaps in artists, albums and clips

The tenth stage, and the first one that is not about a whole domain: the nine
before it emptied `Client\Legacy`, and this one starts closing the distance to
the Python library, which turned out to be larger than the README claimed.

22 models, 15 methods. The gap is down from 47 methods to 32.

## What prompted it

The README said the port was finished. It was not — comparing method names
showed 145 in the reference against 111 here. Nothing returned raw data any
more, and everything the 2019 library had was ported, but seven domains had
never been in it and fifteen newer methods sat in domains that were.

Saying so honestly came first, in its own commit. This stage is the fifteen.

## Methods

All of them go into traits that already exist — no new ones.

| Was | Became | In Python | Difference from the reference |
|---|---|---|---|
| — | `artistsAbout(id): ?AboutArtist` | `artists_about()` | none |
| — | `artistsInfo(id): ?ArtistInfo` | `artists_info()` | none |
| — | `artistsLinks(id): ?ArtistLinks` | `artists_links()` | none |
| — | `artistsClips(id): ?ArtistClips` | `artists_clips()` | none |
| — | `artistsDonation(id): ?ArtistDonations` | `artists_donation()` | none |
| — | `artistsTrailer(id): ?ArtistTrailer` | `artists_trailer()` | none |
| — | `artistsSkeleton(id, skeletonId): ?ArtistSkeleton` | `artists_skeleton()` | none |
| — | `artistsTrackIds(id): list<string>` | `artists_track_ids()` | none |
| — | `artistsDiscographyAlbums(id, page, pageSize, sortBy): ?ArtistAlbums` | `artists_discography_albums()` | none |
| — | `artistsSafeDirectAlbums(id, page, pageSize, sortBy): ?ArtistAlbums` | `artists_safe_direct_albums()` | none |
| — | `artistsDisclaimer(id): list<Disclaimer>` | `artists_disclaimer()` | theirs declares one object; the API sends a list, as it does for tracks and albums |
| — | `albumsTrailer(id): ?AlbumTrailer` | `albums_trailer()` | none |
| — | `albumsSimilarEntities(id): ?AlbumSimilarEntities` | `albums_similar_entities()` | none |
| — | `clipsCredits(id): ?Credits` | `clips_credits()` | none |
| — | `clipsDisclaimer(id): list<Disclaimer>` | `clips_disclaimer()` | the same list, again |
| `BriefInfo::$vinyls` raw | `list<Vinyl>` | typed there too | a debt from the artists stage |

`artistsConcerts` is not here. It answers with concerts, which is a domain of
sixteen models — the same reasoning that kept concerts out of `BriefInfo` two
stages ago. It comes with them.

## Models

`Model/Artist/` gained fourteen: `AboutArtist`, `ArtistInfo`, `ArtistLinks`,
`ArtistLink`, `ArtistClips`, `ArtistClipItem`, `ArtistClipData`,
`ArtistDonations`, `ArtistDonationItem`, `ArtistDonationData`,
`ArtistDonationGoal`, `ArtistTrailer`, `ArtistTrailerStatus`, `ArtistSkeleton`,
plus `Vinyl` for the debt above.

`Model/Skeleton/` is new and shared: `SkeletonBlock`, `SkeletonBlockData`,
`SkeletonTab`, `SkeletonSource`, `SkeletonViewAllAction`. A skeleton is what an
app draws before the contents arrive — the shape of a page, with a source to
fetch each part from. `concertSkeleton` will use the same five.

`Model/Album/` gained `AlbumTrailer` and `AlbumSimilarEntities`.

Two of the new models decide what they hold from their own `type`, the pattern
this library has used since the search stage: `ArtistClipItem` and
`ArtistDonationItem`. An unknown type keeps the type and drops the payload.

## What the live run corrected

- **`Clip::$id`** — a clip carries an id beside its `clipId`, and they are not
  always the same value.
- **`Album::$contentRestrictions`** — the model existed; the album did not
  declare it.
- **`Wave\SimilarEntityData::$album`, `$artist`, `$artists`** — the model was
  written for a playlist's similar entities, which point at waves. An album's
  point at albums and artists.

Two answers that look like failures and are not, both checked against the raw
response:

- **`artistsDonation` answers 404 with an empty body** for an artist who takes
  no donations. That is the endpoint working.
- **`artistsDiscographyAlbums` answers 200 with an empty page** for an artist
  with no discography arrangement, whatever paging or sorting is asked for.

The first of those exposed something worth fixing: an error with no readable
body produced the message `Unknown HTTP error`, with no status. It now names
the status, because that was the only information there was.

## The probe

`examples/artist.php` reads everything the service will say about one artist —
info, about, links, discography, track ids, clips, trailer, donations,
disclaimers and the page layout — then an album's trailer and similar entities
and a clip's credits. It reads only, so it runs without confirmation. Pass an
artist id as the first argument.

It also tries three names for the layout's second identifier, since the
reference documents none: `artist-page`, `artist` and `default` all answer, and
all return the same single `TABS` block.
