# Releasing

Git Flow, and no step of it is improvised.

## The shape of it

`develop` accumulates work. `main` is the released state and nothing lands on it
except through a release branch. A tag on `main` is what Composer resolves a
version from, so a tag is a promise: it cannot be moved once anybody has
resolved it.

## Steps

**1. A release branch off `develop`.**

```
git checkout develop && git pull
git checkout -b release/2.1.0
```

The number is decided here, by what changed:

- a method or model removed, renamed, or given a different meaning — major
- anything added — minor
- only fixes — patch

A new field on a model is a minor, not a patch. Somebody's code may now read it.

**2. Write the changelog entry.**

A section in `CHANGELOG.md`, headed `## [2.1.0] — YYYY-MM-DD`, with a link
definition at the bottom of the file. The release workflow reads the notes from
exactly this section and refuses to publish without one, so the heading has to
match the tag with the `v` dropped.

**3. Whatever else the version claims.** The README's install snippet names a
constraint; a major release changes it.

**4. `make check`, then a pull request into `main`.**

```
https://github.com/LuckyWins/yandex-music-api/compare/main...release/2.1.0?expand=1
```

Note the base: `main`, not `develop`. This is the one branch that goes the other
way, and the compare link git prints on push points at the wrong one.

**5. Tag `main` once the pull request is merged.**

```
git checkout main && git pull
git tag -a v2.1.0 -m 'v2.1.0'
git push origin v2.1.0
```

Pushing the tag is what starts `.github/workflows/release.yml`: it runs the
tests on every supported PHP version, checks that the release tarball carries
the library and nothing else, and only then creates the GitHub release with the
notes from the changelog.

If it fails, there is a tag and no release. Delete the tag locally and on the
remote, fix, and push it again — but only if nothing has resolved it yet.

**6. Merge `main` back into `develop`**, so that the release commits are not
stranded on one branch.

## What is deliberately not done

**Packagist.** The repository is the only distribution channel; consumers add a
`vcs` entry and Composer reads the tags. That is a decision, not an omission.

**A `version` field in `composer.json`.** Composer takes the version from the
tag. A number written into the file disagrees with the tag on the first release
somebody forgets to update it.

**A tag on the 2019 code.** It is version 1 in spirit and stays untagged: its
authentication no longer exists, and `^1.0` resolving to something that cannot
sign in is worse than `^1.0` resolving to nothing.
