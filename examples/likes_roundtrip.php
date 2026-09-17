<?php

declare(strict_types=1);

/**
 * Exercise likes, dislikes and clips against the live API.
 *
 * Likes are the account's own data, so this only ever touches objects it
 * added itself: it reads what is already liked first, skips anything that is
 * there, and removes every mark it sets. Counts are printed before and after
 * so you can see the library came back to where it started.
 *
 * Asks for confirmation first. Pass --yes to skip the prompt, which is also
 * required when stdin is not a terminal.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Support\Bootstrap;
use LuckyWins\YandexMusic\Examples\Support\UnknownFieldCollector;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\Like;
use LuckyWins\YandexMusic\Model\TracksList;

// An artist that has been on the service for years. The album and the
// playlist to mark are taken from the API rather than written down here: an
// id that no longer exists is accepted with `ok` and then quietly ignored,
// which looks exactly like a broken method.
const TRACK_ID = 31190260;
const ARTIST_ID = 4611844;

$confirmed = in_array('--yes', Bootstrap::arguments(), true);

if (!$confirmed) {
    if (!stream_isatty(STDIN)) {
        echo "Refusing to write to the account without confirmation.\n";
        echo "Re-run with --yes if you really mean it.\n";

        exit(1);
    }

    echo "This likes and unlikes a few objects on your account.\n";
    echo "Anything you already like is left alone.\n";
    echo 'Type "run" to continue: ';

    $answer = fgets(STDIN);

    if (!is_string($answer) || 'run' !== trim($answer)) {
        echo "Cancelled. Nothing was changed.\n";

        exit(0);
    }
}

$unknownFields = new UnknownFieldCollector();
$client = Bootstrap::authorizedClient($unknownFields)->init();

/**
 * Add a mark, check the object shows up, remove it, check it is gone.
 *
 * @param callable(): list<string> $read  the ids currently marked
 * @param callable(): bool         $add
 * @param callable(): bool         $remove
 */
function roundTrip(string $label, string $id, callable $read, callable $add, callable $remove): void
{
    $before = $read();

    if (in_array($id, $before, true)) {
        echo "{$label}: already marked, left alone\n";

        return;
    }

    if (!$add()) {
        echo "{$label}: the API refused to add it\n";

        return;
    }

    $appeared = waitFor($id, $read, true);
    $removed = $remove();
    $disappeared = waitFor($id, $read, false);

    printf(
        "%s: added=yes visible=%s removed=%s gone=%s\n",
        $label,
        describe($appeared),
        $removed ? 'yes' : 'NO',
        describe($disappeared),
    );
}

/**
 * Read the list until it agrees that the id is (or is not) there.
 *
 * Some listings lag behind the write that changed them by a second or two, so
 * a single read right afterwards says nothing. Returns how many seconds it
 * took, or null if it never agreed.
 *
 * @param callable(): list<string> $read
 */
function waitFor(string $id, callable $read, bool $wanted, int $seconds = 5): ?int
{
    for ($waited = 0; $waited <= $seconds; ++$waited) {
        if (in_array($id, $read(), true) === $wanted) {
            return $waited;
        }

        sleep(1);
    }

    return null;
}

function describe(?int $waited): string
{
    return match (true) {
        null === $waited => 'NO',
        0 === $waited => 'yes',
        default => "yes (after {$waited}s)",
    };
}

/** @return list<string> */
function trackIds(?TracksList $library): array
{
    if (null === $library) {
        return [];
    }

    return array_map(static fn (string $id): string => explode(':', $id)[0], $library->compositeIds());
}

$albumToLike = $client->artistsDirectAlbums(ARTIST_ID, 0, 1)?->albums[0] ?? null;

/**
 * Someone else's playlist, from the landing.
 *
 * Liking your own does nothing — the API answers `ok` and the playlist never
 * appears in the library — so proving the method works needs a playlist the
 * account does not own.
 */
function editorialPlaylist(mixed $node, int $ownUid): ?string
{
    if (!is_array($node)) {
        return null;
    }

    $uid = $node['uid'] ?? null;
    $kind = $node['kind'] ?? null;

    if (is_int($uid) && is_int($kind) && $uid !== $ownUid) {
        return $uid.':'.$kind;
    }

    foreach ($node as $child) {
        $found = editorialPlaylist($child, $ownUid);

        if (null !== $found) {
            return $found;
        }
    }

    return null;
}

$startLikes = [
    'tracks' => count(trackIds($client->usersLikesTracks())),
    'albums' => count($client->usersLikesAlbums()),
    'artists' => count($client->usersLikesArtists()),
    'playlists' => count($client->usersLikesPlaylists()),
];

echo "\nlikes at the start: ";

foreach ($startLikes as $what => $count) {
    echo "{$what}={$count} ";
}

echo "\n\n-- likes --\n";

roundTrip(
    'track',
    (string) TRACK_ID,
    static fn (): array => trackIds($client->usersLikesTracks()),
    static fn (): bool => $client->usersLikesTracksAdd(TRACK_ID),
    static fn (): bool => $client->usersLikesTracksRemove(TRACK_ID),
);

if (null === $albumToLike) {
    echo "album: skipped, the artist listing gave nothing to mark\n";
} else {
    $albumId = (string) $albumToLike->id;

    roundTrip(
        'album',
        $albumId,
        static fn (): array => array_map(
            static function (Like $like): string {
                $album = $like->album;

                return null === $album ? '' : (string) $album->id;
            },
            $client->usersLikesAlbums(),
        ),
        static fn (): bool => $client->usersLikesAlbumsAdd($albumId),
        static fn (): bool => $client->usersLikesAlbumsRemove($albumId),
    );
}

roundTrip(
    'artist',
    (string) ARTIST_ID,
    static fn (): array => array_map(
        static function (Like $like): string {
            $artist = $like->artist;

            return null === $artist ? '' : (string) $artist->id;
        },
        $client->usersLikesArtists(),
    ),
    static fn (): bool => $client->usersLikesArtistsAdd(ARTIST_ID),
    static fn (): bool => $client->usersLikesArtistsRemove(ARTIST_ID),
);

$pair = editorialPlaylist($client->landing('new-playlists'), (int) $client->getAccountUid());

if (null === $pair) {
    echo "playlist: skipped, the landing offered no playlist to mark\n";
} else {
    roundTrip(
        'playlist',
        $pair,
        static fn (): array => array_map(
            static fn (Like $like): string => $like->playlist?->ownerKind() ?? '',
            $client->usersLikesPlaylists(),
        ),
        static fn (): bool => $client->usersLikesPlaylistsAdd($pair),
        static fn (): bool => $client->usersLikesPlaylistsRemove($pair),
    );
}

echo "\n-- dislikes --\n";

roundTrip(
    'track',
    (string) TRACK_ID,
    static fn (): array => trackIds($client->usersDislikesTracks()),
    static fn (): bool => $client->usersDislikesTracksAdd(TRACK_ID),
    static fn (): bool => $client->usersDislikesTracksRemove(TRACK_ID),
);

roundTrip(
    'artist',
    (string) ARTIST_ID,
    static fn (): array => array_map(
        static function (Like $like): string {
            $artist = $like->artist;

            return null === $artist ? '' : (string) $artist->id;
        },
        $client->usersDislikesArtists(),
    ),
    static fn (): bool => $client->usersDislikesArtistsAdd(ARTIST_ID),
    static fn (): bool => $client->usersDislikesArtistsRemove(ARTIST_ID),
);

// The reference sends this parameter with underscores for dislikes and with
// hyphens for likes. We send hyphens in both; this is where that gets checked.
echo "\n-- revision filtering --\n";

foreach (['likes' => 'usersLikesTracks', 'dislikes' => 'usersDislikesTracks'] as $what => $method) {
    $full = $client->{$method}();
    $revision = $full?->revision;

    if (null === $revision) {
        echo "{$what}: no revision in the answer, nothing to check\n";

        continue;
    }

    $unchanged = $client->{$method}(ifModifiedSinceRevision: $revision);

    printf(
        "%s: revision=%d, asking again with it returns %s\n",
        $what,
        $revision,
        null === $unchanged ? 'nothing, as it should' : 'the whole list again — the filter did NOT work',
    );
}

echo "\n-- clips --\n";

try {
    $liked = $client->usersLikesClips();
    echo 'liked clips   -> '.(null === $liked ? 0 : count($liked->clips))." clip(s)\n";

    $suggested = $client->clipsWillLike(0, 5);
    $suggestedClips = null === $suggested ? [] : $suggested->clips;
    echo 'suggested     -> '.count($suggestedClips)." clip(s)\n";

    $first = $suggestedClips[0] ?? null;

    if (null !== $first && null !== $first->clipId) {
        $byId = $client->clips($first->clipId);
        echo 'by id         -> '.count($byId)." clip(s)\n";
    }
} catch (YandexMusicException $e) {
    echo "clips failed: {$e->getMessage()}\n";
}

echo "\nlikes at the end:   ";

$endLikes = [
    'tracks' => count(trackIds($client->usersLikesTracks())),
    'albums' => count($client->usersLikesAlbums()),
    'artists' => count($client->usersLikesArtists()),
    'playlists' => count($client->usersLikesPlaylists()),
];

foreach ($endLikes as $what => $count) {
    echo "{$what}={$count} ";
}

echo "\n";

if ($endLikes !== $startLikes) {
    echo "\nWARNING: the counts did not come back to where they started.\n";
    echo "Check your likes by hand.\n";
}

echo "\n".$unknownFields->summary();

exit($endLikes === $startLikes ? 0 : 1);
