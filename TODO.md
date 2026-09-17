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

- [ ] **Convert the remaining domains to typed models.** Everything still in
      `Client\Legacy` returns raw decoded arrays. The reference library has 332
      model classes across 21 domains; each stage lifts one domain out of the
      trait, and the port is done when the trait is empty.

- [ ] **Direct download links.** `getDirectLink()` built a URL by md5-ing a
      hardcoded salt. Yandex replaced that with an HMAC-SHA256 signature —
      `utils/sign_request.py` in the reference has the current scheme. The
      method currently throws rather than returning a URL that will not work.

- [ ] **Ynison.** The reference gained a websocket protocol for remote player
      control and cross-device state. Nothing here touches it.
