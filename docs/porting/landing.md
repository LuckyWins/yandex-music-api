# The landing, the feed and the genres

Ported in the ninth stage, which is the last one. Twenty-five models, one enum,
nine methods — and `Client\Legacy` is gone.

## Methods

| Was (`Client\Legacy`) | Became (`Client\Landing`) | In Python | Difference from the reference |
|---|---|---|---|
| `feed(): array` | `feed(): ?Feed` | `feed()` | none |
| `feedWizardIsPassed(): mixed` | `feedWizardIsPassed(): bool` | `feed_wizard_is_passed()` | none |
| `landing(blocks): array` | `landing(BlockType\|string\|list, eitherUserId): ?Landing` | `landing()` | blocks are an enum, and the reference's hardcoded user id is a parameter rather than a constant — see below |
| `genres(): array` | `genres(): list<Genre>` | `genres()` | none |
| — | `chart(?chartOption): ?ChartInfo` | `chart()` | none |
| — | `newReleases(): ?LandingList` | `new_releases()` | none |
| — | `newPlaylists(): ?LandingList` | `new_playlists()` | none |
| — | `podcasts(): ?LandingList` | `podcasts()` | none |
| — | `tags(tagId): ?TagResult` | `tags()` | none |

## Somebody else's user id

The reference pins `eitherUserId=10254713668400548221` into every landing
request — a real account's id, left in the code. Copying that would mean every
consumer of this library quietly asking for a stranger's front page.

Checked against the live API: the same blocks come back with it and without it.
So it is not sent. The parameter exists for callers who have a reason to ask
for someone else's landing, and defaults to not sending one.

## Fields whose type decides what they hold

Three of them this stage, all through the `prepare()` hook the base class has
carried since the tracks stage:

- **`BlockEntity::$data`** — seven kinds, keyed by the entity's `type`:
  `personal-playlist`, `promotion`, `album`, `playlist`, `chart-item`,
  `play-context`, `mix-link`.
- **`Block::$data`** — two kinds: `personal-playlists` carries whether the
  taste wizard was answered, `play-contexts` carries more tracks.
- **`PlayContext::$payload`** — what was being listened to, keyed by the
  context's own `context` field: a playlist, an album or an artist.

The third one was a bug first. The payload was modelled as a playlist, because
that is what the first responses held — and the unknown-field report promptly
filled up with album and artist fields being poured into `Playlist`. That is
precisely the failure the reporting exists to catch.

## The awkward names

**A field called `client`.** `PlayContext` carries the player that was
listening under `client`, which is the name every model already uses for its
back-reference to the API client. The property is `$playedIn`, and `prepare()`
moves the value across; the back-reference stays what it should be.

**Fields that begin with a digit.** `Genre`'s artwork arrives under `20x20`,
`208x208` and `300x300`. A PHP property cannot start with a digit, so they are
`$_20x20` and friends — and the key matching, which ignores separators, lines
them up with the response without any help.

**A map instead of a list.** `Genre::$titles` is keyed by language, so a name
can be read directly: `$genre->titleIn('en')`. The base class has had
`mapFromApi()` since the account stage for exactly this; this is its second
user.

## Fields the live run found

Six, none of them in the reference: `PlayContext::$payload`,
`Block::$playContext` with its three background fields, `Event::$socialTracks`
(tracks surfaced because people you follow liked them, which became
`Feed\SocialTrack`), `Genre::$hideInRegions`, and a third image size on
`Genre\Images`. `PlaylistId` gained `playlistUuid`, which a landing block sends
alongside the pair.

## The end of Legacy

`Client\Legacy` held everything not yet ported, and shrank by one domain per
stage: 47 methods at the start, then 36, 29, 14, 12, 4, and now none. The trait
is deleted, and `tests/Client/PortedSurfaceTest.php` keeps it that way — it
fails if the trait comes back, or if any public method on `Client` starts
returning `mixed` again.

Nine stages, 147 models and 104 client methods later, no endpoint hands back
a decoded array for the caller to guess at.
