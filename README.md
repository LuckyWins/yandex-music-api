# yandex-music-api

An unofficial PHP client for the Yandex.Music API.

> **This is not a Yandex product.** The API it talks to is undocumented and can
> change without notice. Nothing here is endorsed by or affiliated with Yandex.

It is a port of [MarshalX/yandex-music-api][python], the Python library that
reverse-engineered this API and still tracks it. Where the two disagree, the
Python library is right and this one has a bug.

[python]: https://github.com/MarshalX/yandex-music-api

## Status

**Everything the Python library does, this one does.** 221 models, 159 methods,
and every endpoint returns typed models — nothing hands back decoded JSON for
the caller to guess at. The reference has 144 client methods and none of them
is missing here; the extra fifteen are accessors and conveniences of our own.

The method count is checked against the reference by
`php tools/compare-with-reference.php`, which needs a checkout of it beside
this one.

The typed-fields half is the one that can rot, because it rots whenever Yandex
adds a field. So it is checked against the service rather than against the
reference:

```
make audit
```

calls every reading endpoint and reports, by model, every field no model
declares and what shape arrived in it. See [docs/audit.md](docs/audit.md) for
what it does and does not check. It needs a token, and says so rather than
failing when there is none.

It was reached in thirteen stages, one domain at a time, each checked against
the live API rather than against the Python library — which mattered more than
expected, since the reference is wrong in several places the API has moved on
from. Every stage is written up in [docs/porting/](docs/porting/), divergences
included.

Released as 2.0.0, which is what `main` holds; `develop` is where the next one
accumulates. What changed is in [CHANGELOG.md](CHANGELOG.md), and how a version
gets cut in [docs/releasing.md](docs/releasing.md).

What is still open, in [TODO.md](TODO.md): creating a playback queue, which no
body shape has been found for, and Ynison, the websocket protocol for remote
playback.

## Requirements

PHP 8.3 or newer, and a [PSR-18][psr18] HTTP client. If you do not supply one,
Guzzle is used when installed, and otherwise one is discovered.

[psr18]: https://www.php-fig.org/psr/psr-18/

## Installation

The package is not on Packagist. Point Composer at the repository:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/LuckyWins/yandex-music-api" }
    ],
    "require": {
        "luckywins/yandex-music-api": "^2.0"
    }
}
```

```console
composer require luckywins/yandex-music-api guzzlehttp/guzzle
```

## Getting a token

Yandex withdrew the username-and-password grant this library used until 2019.
The only way left is the OAuth device flow: your code asks for a short code, the
user confirms it in a browser, and a token comes back.

```php
use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\DeviceAuth\DeviceCode;

$client = new Client();

$token = $client->deviceAuth(function (DeviceCode $code) {
    echo "Open {$code->verificationUrl} and enter: {$code->userCode}\n";
});

echo $token->accessToken;
```

`deviceAuth()` blocks until the user confirms, the code expires, or the callback
you pass to `shouldCancel` says to stop. It paces itself using the interval the
server asks for.

**Storing the token is your job.** The library never writes it anywhere and
never refreshes it. Treat it like a password: it grants full access to the
account — the profile, the phone numbers on the Yandex ID, the subscription —
and it lasts about a year.

The example script does the storing for you, into a git-ignored `.env.local`
written owner-only. It deliberately never prints the token: anything that
reaches a terminal reaches scrollback and shell history too.
[`.env.local.example`](.env.local.example) documents that file's contents.

If a token does leak, revoke it:

```console
$ php examples/revoke_token.php
Revocation requested.
Confirmed: the API now rejects it.
```

Ending the session in Yandex ID under "Devices and sessions" does not do this —
that list is about sign-ins, and the token keeps working afterwards. In code:

```php
$client->revokeToken();          // this client's own token
$client->revokeToken($leaked);   // any other
```

The endpoint reports success for a token that never existed, so the call proves
nothing by itself. The script therefore checks that an authorized request is now
refused, and only then removes the token from `.env.local`. Do the same if you
revoke by hand.

Already have a token? Pass it in:

```php
$client = new Client('y0_your_token_here');
```

A runnable version of the flow is in [`examples/device_auth.php`](examples/device_auth.php).

## Using it

Constructing a client performs no requests. Endpoints that act on behalf of a
user need the account loaded first:

```php
use LuckyWins\YandexMusic\Model\Search\SearchType;

$client = (new Client($token))->init();

// Typed, because these domains are ported.
echo $client->me()?->account?->login;
echo $client->me()?->plus?->hasPlus ? 'Plus' : 'no Plus';

$album = $client->albumsWithTracks(40926432);
echo $album?->title;

foreach ($client->usersPlaylistsList() as $playlist) {
    echo $playlist->title, ' — ', $playlist->trackCount, " tracks\n";
}

echo count($client->usersLikesTracks()?->tracks ?? []), " liked tracks\n";

$found = $client->search('nirvana', type: SearchType::Track);
echo $found?->tracks?->results[0]->title;

foreach ($client->rotorStationsDashboard()?->stations ?? [] as $offered) {
    echo $offered->station?->name, "\n";
}

foreach ($client->chart()?->chart?->tracks ?? [] as $position) {
    echo $position->chart?->position, '. ', $position->track?->title, "\n";
}
```

`init()` is a separate step on purpose: constructing a client performs no
requests. Call it once before anything that acts on behalf of the user — those
endpoints need the account id it fetches.

Without a token the API still answers, but only with what an anonymous visitor
sees — thirty-second previews instead of whole tracks. Opening
music.yandex.ru in a private window shows you roughly where the line is.

## Downloading a track

```console
$ php examples/download_track.php 31190260
Miyagi & Эндшпиль, KREC — Нирвана
available: mp3 320, mp3 192
taking:    mp3 320

wrote 31190260.mp3 (10.4 MiB)
```

In code:

```php
$variants = $client->tracksDownloadInfo($trackId);

usort($variants, fn ($a, $b) => $b->bitrateInKbps <=> $a->bitrateInKbps);

$variants[0]->download('track.mp3');   // streamed, not buffered
```

Two things to know. A download manifest is good for about a minute, so resolve
it and fetch promptly rather than collecting manifests for later. And the audio
does not come from the API host — it is served by redirect from Yandex's
streaming hosts, so a network that reaches `api.music.yandex.net` but not those
will fail here even though everything else works.

## Errors

Everything the library raises descends from `YandexMusicException`:

```
YandexMusicException
├── UnauthorizedException        401, 403 — token missing, expired, or insufficient
├── InvalidBitrateException
├── IdMissingException
├── DeviceAuthException          the device flow was refused, cancelled or timed out
└── NetworkException             transport failures and unmapped statuses
    ├── BadRequestException      400
    ├── NotFoundException        404
    └── TimedOutException
```

Where the server named a machine-readable error code, it is on the exception
rather than only in the message, so you can branch on it:

```php
try {
    $client->pollDeviceToken($deviceCode);
} catch (DeviceAuthException $e) {
    if ($e->getErrorCode() === 'expired_token') {
        // start over
    }
}
```

## Noticing when the API changes

Yandex adds and renames response fields without announcement. Models drop what
they do not recognize, but they can tell you about it first:

```php
$client = new Client($token, reportUnknownFields: true, logger: $logger);
```

Any field arriving that no model declares is logged through your PSR-3 logger,
with the model and field names. That warning is how a change on Yandex's side
gets caught before it turns into a missing value somewhere downstream.

## Configuring the HTTP client

Supply your own when you need control over timeouts, proxies or middleware:

```php
use GuzzleHttp\Client as Guzzle;
use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Http\Request;

$request = new Request(new Guzzle([
    'timeout' => 10,
    'proxy' => 'http://127.0.0.1:8080',
]));

$client = new Client($token, $request);
```

PSR-18 has no notion of a timeout, so the five-second default applies only to
the client this library builds for itself. A client you inject keeps whatever
you configured on it.

## Networks where Yandex is unreachable

On a corporate VPN or a filtered network, every request may simply hang until it
times out. One cause worth knowing about, because it is invisible from the error
message: some networks block Yandex over IPv4 while leaving IPv6 alone, and the
system resolver hands out only the IPv4 address.

Check whether that is what is happening. If `getaddrinfo` reports no IPv6 address
while a direct DNS query does, this is it:

```console
$ python3 -c "import socket;[print(a[4][0]) for a in socket.getaddrinfo('oauth.yandex.ru',443)]"
87.250.251.227

$ host -t AAAA oauth.yandex.ru
oauth.yandex.ru has IPv6 address 2a02:6b8::15e
```

The fix is to reach the API by an address the resolver will not give you. Pin
them on a client of your own — no change to this library is needed, which is
what the PSR-18 seam is for:

```php
use GuzzleHttp\Client as Guzzle;
use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Http\Request;

$guzzle = new Guzzle([
    'timeout' => 10,
    'curl' => [
        CURLOPT_RESOLVE => [
            'oauth.yandex.ru:443:[2a02:6b8::15e]',
            'api.music.yandex.net:443:[2a02:6b8::5:246]',
        ],
    ],
]);

$client = new Client($token, new Request($guzzle));
```

Look the current addresses up rather than copying those — Yandex changes them:

```console
$ host -t AAAA oauth.yandex.ru
$ host -t AAAA api.music.yandex.net
```

Putting the same hosts in `/etc/hosts` fixes every tool at once, `curl` included,
at the cost of needing root and of going stale silently when the addresses move.

## Reference

- [docs/models.md](docs/models.md) — every model, its fields and what nests
  inside what
- [docs/endpoints.md](docs/endpoints.md) — every method, the request it makes
  and what it returns
- [docs/porting/](docs/porting/) — one record per domain: what the methods were,
  what they became, and where we deliberately differ from the Python library

The first two are generated from the source and checked in CI, so they cannot
drift from the code. The third is written by hand, because decisions are not
derivable.

## Development

```console
make install     install dependencies
make test        run the test suite
make stan        static analysis, PHPStan level 9
make cs-fix      fix code style
make check       everything CI would run
```

CI runs the suite against every supported PHP version on pull requests into
`develop` and `main`, and runs PHPStan and the style check once on 8.3. That
matrix is the only thing verifying the `^8.3` constraint in `composer.json`,
since development happens on a single version.

Pushing a version tag runs it all again and then publishes the release, with
the notes taken from `CHANGELOG.md` — see [docs/releasing.md](docs/releasing.md).

Tests never touch the network and never need credentials: HTTP is mocked at the
PSR-18 boundary, and time is injected, so polling loops run instantly. Only the
scripts in `examples/` talk to the real API, and they read the token from
`.env.local` or the `YANDEX_MUSIC_TOKEN` environment variable.

On macOS, PHP 8.3 from Homebrew is keg-only and not on `PATH`. Either add
`/opt/homebrew/opt/php@8.3/bin` to it, or run `make PHP=/opt/homebrew/opt/php@8.3/bin/php`.

## License

LGPL-3.0-or-later, the same license as the Python library this is ported from.
See [LICENSE](LICENSE).
