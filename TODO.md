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

- [ ] **Close the gap with the Python library.** It has 144 client methods;
      this one has 127. Nothing here returns raw data any more, and everything
      the 2019 library had is ported — but seven domains were never in it and
      are still missing, along with fifteen newer methods in domains that are
      ported.

      In the order they are being done:

      1. ~~gaps in ported domains~~ — done, see
         `docs/porting/artists-albums-clips-gaps.md`
      2. `pins`, `queue`, `presaves` — 7 models, 16 methods
      3. `concerts` — 16 models, 6 methods, and with it `artistsConcerts`,
         which returns concerts and was deferred for that reason
      4. `metatags`, `labels`, `music_history` — 21 models, 10 methods

      The count is checked rather than remembered: compare the `def` names in
      the reference's `_client/` against this library's public methods.

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

- [ ] **Ynison.** The reference gained a websocket protocol for remote player
      control and cross-device state. Nothing here touches it.
