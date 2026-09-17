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

## Credentials

An OAuth token grants full access to the account behind it — the profile, the
phone numbers on the Yandex ID, the subscription — and lasts about a year.

- The token lives in `.env.local`: git-ignored, written owner-only, loaded by
  `examples/Support/Bootstrap.php`. Nothing prints it, and nothing commits it.
- `.env.local.example` is its committed twin and documents every variable the
  project understands — what it is, what breaks without it, how to obtain it.
  A new variable lands there in the same change that starts reading it, and the
  placeholder value stays obviously fake.
- **The assistant does not read `.env.local` and does not need to.** A `deny`
  rule in `.claude/settings.json` blocks the Read tool from it. The rule cannot
  cover every shell command, so this is also a standing instruction.
- **Checks against the live API are run by the user, not by the assistant.**
  The assistant writes the script; the user runs it and reports what happened.
- Tests never take credentials and never reach the network. HTTP is mocked at
  the PSR-18 boundary and time is injected, so nothing in `make check` needs a
  token or a connection.
- If a token is ever pasted into a conversation, a log, or a commit, it is
  compromised: revoke it rather than reasoning about who saw it. Ending the
  session in Yandex ID does not do it — the token stays valid. Post it to
  `https://oauth.yandex.ru/revoke_token` with the client id and secret from
  `Client\DeviceAuth`, then confirm `/account/status` answers 401, because that
  endpoint replies `{"status": "ok"}` even for a token that never existed.

## Porting a domain

The library is being ported from the Python reference one domain at a time. Each
such stage ends with a table of what the client's methods were and what they
became, with a column for the Python equivalent:

| Was | Became | In Python | Difference from the reference |

The third and fourth columns are the point of it. They make every divergence a
decision on the record rather than a drift nobody noticed, and they make it
obvious when a method is ours rather than the reference's — which is usually the
sign that it should be deleted rather than ported.

## Rules

- Deferred tasks, rejected options and the reasoning behind them live in
  `TODO.md`
- Written in English: everything in the repository — code, comments,
  `CLAUDE.md`, `TODO.md`, `README.md`, tests, config files, commit messages
- Written in Russian: conversation with the user

## Branches

Git Flow.

- `main` — released state, and since 2.0.0 that is the modernized library
  rather than the 2019 one. Renamed from `master`; the old name survives only
  in links predating the rename.
- `develop` — integration branch, where the next release accumulates
- `feature/*` — one branch per chunk of work, off `develop` and back into it:
  `feature/composer-skeleton`, `feature/http-client`, `feature/track-models`
- `release/*` — off `develop` and into `main`, the one branch that goes that
  way. See [docs/releasing.md](docs/releasing.md) for what happens on it.
- `hotfix/*` — as Git Flow defines them

Branch names are lowercase, words separated by hyphens, and describe the work
rather than the ticket.

Link a finished branch for review with the base already chosen:

    https://github.com/LuckyWins/yandex-music-api/compare/develop...<branch>?expand=1

The `pull/new/<branch>` form git prints on push defaults the base to `main`,
which has to be changed by hand — and a missed change puts every commit since
the last release into the diff instead of the one stage under review. A
release branch is the exception: its base really is `main`.

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
