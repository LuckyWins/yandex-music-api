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

## CI

- [ ] **Run the test suite on pull requests.** A GitHub Actions workflow with a
      matrix over the supported PHP versions — `shivammathur/setup-php`,
      `composer install`, then `make check`. The reference library runs a
      21-cell matrix across three operating systems; one OS and the supported
      PHP versions is enough here.

      Separate from the release workflow above, and worth having sooner: it
      catches a version-specific break long before anything is tagged.

---

## Port

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
