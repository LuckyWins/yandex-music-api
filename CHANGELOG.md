# Changelog

Notable changes to this library, newest first. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and the version
numbers follow [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] — 2026-09-17

A rewrite. The 2019 library is gone rather than extended, and nothing written
against it will run against this one.

That library was not a Composer package at all: `src/client.php` was included
by hand, its `Client` class sat in the global namespace, and its sixty-three
methods handed back whatever `json_decode` had produced. This one is a package
with PSR-4 autoloading, declared types throughout, and typed models on every
endpoint.

### Added

- **221 models**, one class per API object, grouped by domain. Every field is
  declared and typed; optional ones are nullable rather than absent.
- **159 client methods** across 18 domain traits — everything the Python
  reference has, checked by `tools/compare-with-reference.php` rather than
  remembered.
- **The OAuth device flow** (`deviceAuth()`), which is the only way left to
  obtain a token.
- **Unknown-field reporting.** A model that receives a field nobody declared
  says so, with the field's name and shape. `make audit` walks every reading
  endpoint with it switched on — see [docs/audit.md](docs/audit.md).
- **1305 tests**, none of which reaches the network or needs a credential.
  PHPStan at level 9 and a style check run alongside them, on PHP 8.3, 8.4
  and 8.5.
- **Generated reference** under `docs/`, regenerated and verified by CI so it
  cannot quietly go stale.

### Changed

- Namespaced as `LuckyWins\YandexMusic`, autoloaded through PSR-4. There is no
  file to include.
- HTTP goes through a [PSR-18](https://www.php-fig.org/psr/psr-18/) client,
  discovered when one is not supplied, rather than through hand-rolled cURL.
- Unsuccessful responses raise typed exceptions instead of returning `false` or
  an array with an error in it.
- PHP 8.3 is the minimum. The old code targeted PHP 5 idioms.

### Removed

- **Signing in with a username and password.** Yandex withdrew that grant; the
  old `fromCredentials()` cannot work and has no replacement beyond the device
  flow.
- The `config.php` / `index.php` / `test.php` scaffolding, which belonged to a
  demonstration rather than to a library.

### Notes

The 2019 code was never tagged, so version 1 has no release to install. It
remains in the history, and deliberately not under a tag: its authentication no
longer exists, and a dead end that Composer can resolve is worse than one it
cannot.

[2.0.0]: https://github.com/LuckyWins/yandex-music-api/releases/tag/v2.0.0
