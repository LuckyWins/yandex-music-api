# Pins, queues and presaves

The eleventh stage. Seven models, sixteen methods, three domains that were
never in the 2019 library — so nothing was ported here, only written.

The gap with the Python library is down from 32 methods to 16.

## Methods

| Was | Became | In Python | Difference from the reference |
|---|---|---|---|
| — | `pins(): ?PinsList` | `pins()` | none |
| — | `pinAlbum(id): ?Pin` / `unpinAlbum(id): bool` | `pin_album()` / `unpin_album()` | none |
| — | `pinArtist(id): ?Pin` / `unpinArtist(id): bool` | `pin_artist()` / `unpin_artist()` | none |
| — | `pinPlaylist(uid, kind): ?Pin` / `unpinPlaylist(uid, kind): bool` | `pin_playlist()` / `unpin_playlist()` | none |
| — | `pinWave(seeds): ?Pin` / `unpinWave(seeds): bool` | `pin_wave()` / `unpin_wave()` | seeds go as a list; theirs sends a string, which the server refuses by hanging up |
| — | `queuesList(?device): list<QueueItem>` | `queues_list()` | the device travels with the request rather than staying in the client |
| — | `queue(id): ?Queue` | `queue()` | none |
| — | `queueUpdatePosition(id, currentIndex, ?device): bool` | `queue_update_position()` | the same device handling |
| — | `queueCreate(Queue, ?device): ?string` | `queue_create()` | takes the model rather than a JSON string — and does not work; see below |
| — | `usersPresaves(?userId, includeReleased, includeUpcoming): ?Presaves` | `users_presaves()` | none |
| — | `usersPresavesAdd(albumId, likeAfterRelease, ?userId): bool` | `users_presaves_add()` | none |
| — | `usersPresavesRemove(albumId, ?userId): bool` | `users_presaves_remove()` | none |

## What the HTTP layer gained

**JSON on PUT and DELETE.** Pinning is `PUT /pin/album` with `{"id": ...}` and
unpinning is `DELETE` with the same body — a verb that rarely carries one.
`Request::putJson()` and `deleteJson()` join `postJson()` from the radio stage.

**Headers for one request.** A queue belongs to a device, and the endpoints
want `X-Yandex-Music-Device`. The reference writes it into the client's shared
headers, where it stays and travels with every later request. Here it is passed
to the call that needs it: `get()`, `post()` and `postJson()` take an optional
`$headers`.

**A device to describe.** `Client` takes a `device` string, defaulting to one
that names this library. The identifiers stay the literal `random` the
reference sends — nothing is known to read them, and inventing plausible ones
would be worse than saying nothing.

## A wave is pinned by a list

`pinWave('user:onyourwave')` sent the seeds as a string, as the reference does,
and the server **closed the connection without answering** — `cURL error 52`.
The same call with `{"seeds": ["user:onyourwave"]}` answers 200. So the seeds
always travel as a list here, and a bare string is wrapped.

Two things follow. The reference's `pin_wave` cannot work as written. And an
empty reply, rather than a status, is how this API says a body is the wrong
shape — worth remembering, since nothing about it looks like a validation
error.

## Creating a queue does not work

`POST /queues` was tried five ways: JSON with nulls, JSON without them, JSON
without `from`, the raw JSON string the reference sends with a form content
type, and that string as a form field. Every one was refused —
`400 Can't parse body` for the JSON ones, a closed connection for the rest.

Reading works: `queuesList()` and `queue()` answer normally, and
`queueUpdatePosition()` is untested for the same reason there is nothing to
update.

The method stays, with the failure documented on it. What the endpoint wants is
most likely the protobuf the current apps speak, and finding that out is not
this stage's work. It is in `TODO.md`.

## Small things the live run taught

- **Pin kinds come back suffixed.** You pin with `/pin/album` and get back a
  pin of type `album_item`. `PinsList::ofType()` accepts either spelling,
  because being handed an empty list for asking the obvious way helps nobody.
- **A pinned wave has no id.** It describes itself instead: a station id, the
  seeds, colours, an animation. Those are fields of `PinData` too — one model
  for four kinds, with the pin's type saying which half to read.
- **Presave flags are lowercase.** `true` and `false`, where the account,
  search and likes endpoints take `True` and `False` capitalized. The API's
  inconsistency, not ours.

## The probe

`examples/pins_queue.php` pins an album, an artist, a playlist and a wave,
reads the list, unpins all four and checks the list came back to where it
started; creates a queue and moves its position; presaves an album and removes
it. It asks for confirmation, and everything it sets it unsets — except the
queue, which has no delete endpoint and is only a record of what a device was
playing.
