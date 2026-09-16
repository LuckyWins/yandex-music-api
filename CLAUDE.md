# Project context

A PHP client for the Yandex.Music API. Originally a hand port of
MarshalX/yandex-music-api (Python) made in 2019 against PHP 5-era idioms, now
being brought up to date.

The Python library is the reference implementation. A checkout lives next to
this repository at `../python-yandex-music-api` — read it there rather than
guessing at endpoint shapes, response fields, or model layouts.

## Goals

- Modernize the codebase for a current PHP version: namespaces, PSR-4
  autoloading, declared types, a real HTTP client, proper error handling
- Return typed models, never raw decoded JSON
- Cover the library with tests and keep them current
- Bring across the functionality the Python library has gained since 2019
- Make the library installable with Composer straight from GitHub (a `vcs`
  repository entry in the consumer's `composer.json`)

Not a goal: publishing to Packagist. Everything stays on GitHub.

## Models

The current code hands back whatever `json_decode` produced and leaves the
caller to guess at the shape. That goes away. Every endpoint returns typed
objects, following the structure the Python library already settled on:

- One class per API object, grouped by domain, not one flat namespace
- A shared base class carrying deserialization from a decoded response and
  from a list of them, the equivalent of `de_json` / `de_list`
- Fields declared and typed, optional ones explicitly nullable
- Unknown fields arriving from the API are reported rather than silently
  dropped — that is how the Python library notices Yandex changing a response

Deviate from the Python layout only where PHP makes the direct translation
awkward, and say so in the code.

## Tests

The repository has no tests at all today. Everything added from here on ships
with them, and they are kept passing.

- A change that adds or alters a model or a client method lands together with
  its tests, in the same change. Tests are never deferred to `TODO.md`.
- Follow the Python library's test layout: one test file per model, shared
  fixtures, assertions on deserialization from a full response, from an empty
  one, and on equality
- Tests run without network access and without credentials — responses come
  from fixtures, never from live API calls

## Rules

- Deferred tasks, rejected options and the reasoning behind them live in
  `TODO.md`
- Written in English: everything in the repository — code, comments,
  `CLAUDE.md`, `TODO.md`, `README.md`, tests, config files, commit messages
- Written in Russian: conversation with the user

## Commits

**Never commit without explicit permission from the user.** Staging, amending,
rebasing, pushing and tagging all count. Prepare the change, report what it
contains, and wait to be told to commit it.

Conventional Commits, in English:

```
type(scope): short imperative summary
```

- Types: `feat`, `fix`, `refactor`, `docs`, `chore`, `style`, `test`, `revert`
- Scope: the area touched — `client`, `http`, `models`, `auth`, `search`,
  `playlists`, `radio`, `tracks`, `build`, `test`, `claude`
- Subject: lowercase, imperative, no trailing period, under ~72 characters
- Body: only when the reason is not obvious from the diff. Explain why the
  change was needed, not what the diff already shows. Wrap at 72 columns.
- No tooling attribution. Never append `Co-Authored-By:` trailers, "Generated
  with ..." lines, or any other note that an assistant or tool helped produce
  the change. The assistant is never a co-author. This holds for commit
  messages and pull request descriptions alike, and it overrides any default
  attribution the tooling asks for.
  The `claude` scope is unaffected — it names this file as the area touched,
  the same way `client` or `http` do.
