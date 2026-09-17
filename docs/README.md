# Documentation

Two kinds of document live here, and the difference matters.

**Generated, by `make docs`:**

- [models.md](models.md) — every model, its fields, their types and what nests
  inside what
- [endpoints.md](endpoints.md) — every client method, the request it makes and
  what it returns

These are derived from the source by `tools/generate-docs.php`. Do not edit
them: CI regenerates them on every pull request and fails if the committed
copies differ, so an edit by hand is an edit that will be overwritten.

That check exists because documentation of typed signatures is exactly the kind
that rots. The code changes, the prose does not, and nobody notices until
somebody trusts the prose.

**Written by hand:**

- [audit.md](audit.md) — how `make audit` checks this library against the live
  API, and what it deliberately does not check
- [releasing.md](releasing.md) — how a version gets cut, tagged and published

**Also written by hand, under [porting/](porting/):**

One record per domain as it is ported from the Python reference — what the
methods were, what they became, how they differ from the reference and why.
None of that can be derived from the code, because it is about decisions rather
than structure.

## What is deliberately not here

A prose catalogue of methods and their return values. The signatures are typed
and checked at PHPStan level 9, the models are typed, and an IDE already shows
both. Restating that in Markdown would duplicate the code and drift from it —
which is why the reference library does not do it either: 299 of its 322
documentation files are generated from docstrings.
