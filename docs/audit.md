# Auditing the library against the live API

```
make audit
```

Calls every reading endpoint on the live API and reports, model by model, every
field that arrived with nowhere to go.

It answers the claim the README makes that every field is typed rather than
handed back as decoded JSON. That was true when it was written, and it stops
being true the day Yandex adds a field.

## Why the reference is not consulted

The Python library was the map while this one was being ported, and the port is
finished. From here it is a second-hand account of the same service: it can be
behind, and it has been — the radio endpoints it sends forms to now want JSON,
and `pin_wave` refuses the string it sends. The service is the only thing worth
checking against, and where the two disagree the service wins.

`tools/compare-with-reference.php` is still in the repository for a one-off
comparison of the method lists, and needs a checkout of
[MarshalX/yandex-music-api][python] beside this one. It is not part of this
audit.

[python]: https://github.com/MarshalX/yandex-music-api

## What it needs

A token in `.env.local` and a network. Without a token it says so and exits
cleanly, which is also why `make audit` is not part of `make check`: CI has
neither, and a check that cannot run in CI has no business failing it.

## Three rules the sweep keeps

**It does not write.** Only reading endpoints are called. The writing ones are
listed in the script with a reason in a word, and that list is printed as part
of the report — a skipped endpoint is a decision on the record, not a silent
omission. Writing is exercised by the domain probes instead, each of which
undoes what it did.

**It does not print values.** Names and shapes only. An unplaced field is as
likely to hold a phone number as a feature flag, and these reports are meant to
be safe to paste into an issue.

**It does not hardcode identifiers.** This API accepts a dead id in silence, so
a hardcoded one that stops existing looks exactly like a broken method — a
mistake that cost a whole stage once. Everything is found by following the API:
a search gives an artist, the artist gives albums, the albums give tracks and
labels, the account gives playlists and queues.

## Reading the report

Three sections at the end.

**Unknown fields**, by model: the name, the shape, and the calls it arrived on.

The shape decides the fix — a scalar is a property, an `object` is a model, a
`list<object>` is a `NESTED` list. `empty` means something arrived but was
empty, and `json_decode` cannot tell an empty list from an empty object, so the
shape needs a second sample. A field seen as two shapes is printed as
`string|object`, which means the API is not consistent about it and neither can
the model be.

The calls are there because a field that turns up once in a hundred calls is
otherwise a hunt through all hundred to find the response that carried it.

**Answered with an error.** Not necessarily a bug: an artist with no donations
answers 404, and that is the API saying no rather than the library asking
wrong. Worth reading, not worth panicking over.

**Not covered.** Endpoints the sweep neither called nor deliberately skipped.
This is the part that keeps the sweep honest — endpoints are taken from the
client by reflection, so one added later shows up here until somebody adds it
to the walk. An empty list is the result to want.

An endpoint that answered with nothing is marked too. It was reached, but no
fields arrived, so nothing about it was really checked.

## What a clean run looks like

Two endpoints answer with an error every time, and neither is a bug:

- `usersSettings` — `/users/{uid}/settings` was retired by the service. Use
  `accountSettings()`, which reads `/account/settings` and works.
- `artistsDonation` — 404 means the artist takes no donations, which is most of
  them. Checked against five artists picked from the clip feed: all four-oh-four.

And one endpoint is normally not covered:

- `queue` — there has to be a queue on this device to read one, and creating
  one is a write. It is covered on an account that has been playing something
  through a client that keeps queues.

Anything beyond those three deserves a look.

## What it does not check

That the fields the models do declare still mean what they meant. A field that
keeps its name and changes its meaning passes this sweep, and no automated
check will catch it.

That the writing endpoints still work. They are exercised by the probes in
[../examples/](../examples/), which ask before they write.

That the values are sensible. Only whether every key has somewhere to go.
