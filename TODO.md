# TODO — ideas for later

Everything deliberately postponed. The current work plan is not duplicated
here; this is only what to come back to.

---

## Distribution

- [ ] **Build a package through GitHub CI.** The library is installed straight
      from GitHub with a `vcs` repository entry, which is enough for now — tags
      are resolved as versions and no build step is involved.

      Later this could become a proper release workflow: a GitHub Actions job
      that runs on a tag, validates `composer.json`, runs the test suite
      against the supported PHP versions, and attaches a release. Publishing
      to Packagist stays off the table — the repository is the only
      distribution channel.

      Not now. It only earns its place once the library is stable enough that
      tags mean something.

---

## Port

- [ ] **A live sweep that finds fields the models are missing.** Unknown-field
      reporting already exists, and `examples/playlist_roundtrip.php` prints a
      summary of what one domain sent that no model declares. What is missing
      is one script that walks every endpoint, with reporting on, and prints
      the whole list at once — so a change on Yandex's side is found by running
      something rather than by noticing it during the next port.

      It needs care in two places. It must not write: reading endpoints only,
      with the mutating ones left to the domain probes that clean up after
      themselves. And it must print names and types, never values, because an
      account's own responses carry personal data.

      The playlists stage is the argument for it: two models turned out to be
      behind the API, and only a run with reporting on showed it.

- [ ] **Creating a playback queue.** `queueCreate()` is written and refused:
      `POST /queues` answers `400 Can't parse body` to JSON and closes the
      connection on anything else. Five body shapes were tried, including the
      one the reference library sends, which fails the same way. Reading
      queues works. The likely answer is that current clients speak protobuf
      here — which is also what Ynison below uses, so the two may be one piece
      of work.

      What was tried, so the next attempt need not repeat it: JSON with null
      fields, JSON without them, JSON without `from`, the raw JSON string with
      a form content type, and that string as a form field. The JSON ones
      answer `400 Can't parse body`; the others close the connection with no
      reply, which is how this API reacts to a body of the wrong shape — the
      same way `/pin/wave` reacts to seeds sent as a string.

      Worth trying next: capturing what the Android app actually sends, and
      `yandex_music/ynison` in the reference, which already speaks the
      protocol.

- [ ] **Ynison.** The reference gained a websocket protocol for remote player
      control and cross-device state. Nothing here touches it.
