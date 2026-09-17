# Radio

Ported in the eighth stage. Fourteen models, four enums, eleven methods — and
two things the reference library gets wrong against the current API.

## Methods

| Was (`Client\Legacy`) | Became (`Client\Radio`) | In Python | Difference from the reference |
|---|---|---|---|
| `rotorAccountStatus(): ?Status` | unchanged, moved into the trait | `rotor_account_status()` | none |
| `rotorStationsDashboard(): array` | `rotorStationsDashboard(): ?Dashboard` | `rotor_stations_dashboard()` | none |
| `rotorStationsList(language): mixed` | `rotorStationsList(?language): list<StationResult>` | `rotor_stations_list()` | ours defaulted to `en`; the default is now the client's own language |
| `rotorStationGenreInfo(genre): mixed` | `rotorStationInfo(string\|Id): list<StationResult>` | `rotor_station_info()` | ours only ever reached genre stations: `('rock')` becomes `('genre:rock')`, and any station works |
| `rotorStationGenreTracks(genre): mixed` | `rotorStationTracks(station, settings2, queue): ?StationTracksResult` | `rotor_station_tracks()` | the same widening, plus the queue parameters |
| `rotorStationGenreFeedback(genre, type, from)` | `rotorStationFeedback(station, FeedbackType\|string, timestamp, from, trackId, totalPlayedSeconds, batchId)` | `rotor_station_feedback()` | typed event, and the four fields we never sent; **JSON body** |
| `rotorStationGenreFeedbackRadioStarted(...)` | `rotorStationFeedbackRadioStarted(station, from, batchId)` | same | any station |
| `rotorStationGenreFeedbackTrackStarted(...)` | `rotorStationFeedbackTrackStarted(station, trackId, batchId)` | same | any station |
| — | `rotorStationFeedbackTrackFinished(station, trackId, totalPlayedSeconds, batchId)` | same | none |
| — | `rotorStationFeedbackSkip(station, trackId, totalPlayedSeconds, batchId)` | same | none |
| — | `rotorStationSettings(station, moodEnergy, diversity, type): bool` | `rotor_station_settings2()` | theirs is named for a version while posting to `/settings3`; ours drops the digit. **JSON body** |

`Legacy` is down to four methods — the landing, and then the port is done.

## The reference's radio feedback does not work

Every feedback request was refused with `400 condition is not met`, whatever
was in it. Seven shapes were tried — with and without the batch id, with the
session id, with an integer timestamp, a float one, none at all — and all seven
were refused.

The body encoding was the answer. **These endpoints take JSON; a form is
refused.** Feedback answers 400 to a form, and `/settings3` answers 415,
Unsupported Media Type. The reference library sends forms to both, so its radio
feedback and station tuning are currently dead — the same kind of thing as the
disclaimer list two stages ago, and worth remembering: our own port is checked
against the API, not against the reference.

`Request::postJson()` exists for this. Everything else in the library still
posts forms, because everything else still takes them.

## What the service accepts, versus what it advertises

Each station's `Restrictions` list the values its settings take. For diversity
a station advertises `default, diverse, favorite, popular` — and the endpoint
refuses `diverse`:

```
Cannot deserialize value of type Diversity2 from String "diverse":
not one of the values accepted for Enum class: [discover, favorite, default, popular]
```

So the advertised vocabulary and the accepted one differ by one value. The
enums here follow the endpoint, since that is what decides whether a request
works, and the discrepancy is why they are not the only thing that can be sent.

The full vocabularies, taken from the service by handing it an impossible value
and reading the complaint:

| Setting | Accepted |
|---|---|
| `language` | russian, not-russian, without-words, any |
| `diversity` | default, discover, favorite, popular |
| `moodEnergy` | all, sad, calm, active, fun |
| `type` | not an enum — anything is accepted |

## Enums that cannot go stale

The question this stage opened with was whether to make the settings enums and
collapse anything unrecognized into an `Unknown` case. They are enums, but
there is no `Unknown`: that case would throw away the actual value, leaving
nothing to display, log, or send back.

Instead:

- **input** takes `Enum|string`, so a value the service adds tomorrow works
  without touching the library;
- **models** keep the raw string, always;
- **typed access** sits beside it and returns `?Enum`, where null means "not
  one of the ones we know" rather than "empty".

```php
$settings->diversity;          // 'diverse' — exactly what arrived
$settings->diversityOption();  // null: advertised by stations, refused by the API
```

`FeedbackType` works the same way, though its four values are fixed by the
protocol rather than advertised.

## Models

`Model/Rotor/` gained `Station`, `StationResult`, `StationTracksResult`,
`Dashboard`, `Sequence`, `Id`, `Restrictions`, `RotorSettings`, `Enum`,
`DiscreteScale`, `Value`, `AdParams` and `TrackParameters`, plus `Model/Icon`,
which is shared rather than radio's own.

`Enum` keeps the reference's name. It is a legal class name in PHP despite
`enum` being a keyword.

## Fields the live run found

Five things the reference does not model, all read off live responses:

- **`Station::$restrictions2`** — the same restrictions in a newer arrangement,
  without the scales, so it reuses `Restrictions`
- **`Sequence::$trackParameters`** — `{bpm, hue, energy}`, which became
  `TrackParameters`
- **`StationTracksResult::$radioSessionId`** — a string identifying the session
- **`Value::$imageUrl`, `$unspecified`, `$serializedSeed`** — sent only in the
  newer `restrictions2`
- **`Plus::$migrated`** — only ever seen null, so its type is a reading of the
  name rather than an observation

## A bug this stage exposed

`/rotor/account/status` answers with a subscription that has no
`hadAnySubscription`, and our `Account\Subscription` declared that field
required — so the very first call of the probe threw. The field is optional
now, with a test pinning it.

The reference declares it required too, and would fail the same way. It was
copied from there during the account stage and had never been exercised by a
response that omits it.

## The probe

`examples/radio.php` reads the dashboard, the station list, one station and its
tracks, prints what each setting accepts, and then reports four events for a
single track before stopping. One play lands in the account's history, which is
why it asks for confirmation first. Pass a station as the first argument.
