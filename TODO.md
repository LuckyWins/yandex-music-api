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
