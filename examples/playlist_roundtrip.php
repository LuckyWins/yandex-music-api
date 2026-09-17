<?php

declare(strict_types=1);

/**
 * Exercise every playlist method that writes, on a playlist made for the
 * purpose and deleted at the end.
 *
 * The reading methods can be checked against anything; the writing ones cannot
 * be checked at all without writing. So this creates a throwaway playlist,
 * renames it, hides it, describes it, puts a track in, takes the track out,
 * and deletes the playlist — printing what the API answered at each step.
 *
 * Your own playlists are never touched. The only trace left is one playlist
 * that exists for the duration of the run, and the final count is printed so
 * you can see it is gone.
 *
 * Asks for confirmation first. Pass --yes to skip the prompt, which is also
 * required when stdin is not a terminal.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Bootstrap;
use LuckyWins\YandexMusic\Examples\UnknownFieldCollector;
use LuckyWins\YandexMusic\Exception\NotFoundException;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\Landing\TrackId;

// A track that has been on the service for years, used only as something to
// insert. Any track would do.
const TRACK_ID = 31190260;
const ALBUM_ID = 4243617;

$confirmed = in_array('--yes', $argv, true);

if (!$confirmed) {
    if (!stream_isatty(STDIN)) {
        echo "Refusing to write to the account without confirmation.\n";
        echo "Re-run with --yes if you really mean it.\n";

        exit(1);
    }

    echo "This creates a playlist on your account, changes it, and deletes it.\n";
    echo "Nothing else of yours is touched.\n";
    echo 'Type "run" to continue: ';

    $answer = fgets(STDIN);

    if (!is_string($answer) || 'run' !== trim($answer)) {
        echo "Cancelled. Nothing was created.\n";

        exit(0);
    }
}

// Reporting is on for the whole run: if the API sends a field no model
// declares, this is where it shows up.
$unknownFields = new UnknownFieldCollector();
$client = Bootstrap::authorizedClient($unknownFields)->init();
$title = 'api-probe '.date('Y-m-d H:i:s');

$before = count($client->usersPlaylistsList());
echo "playlists before: {$before}\n\n";

$playlist = $client->usersPlaylistsCreate($title, 'public');
$kind = $playlist?->kind;

if (null === $kind) {
    echo "Creating the playlist failed; nothing to clean up.\n";

    exit(1);
}

echo "created  kind={$kind} \"{$title}\" revision={$playlist?->revision}\n";

// Everything past this point has something to clean up, so failures have to
// fall through to the delete rather than abort the script.
$failure = null;

try {
    $renamed = $client->usersPlaylistsName($kind, $title.' (renamed)');
    echo "rename   -> {$renamed?->title}\n";

    $hidden = $client->usersPlaylistsVisibility($kind, 'private');
    echo "hide     -> {$hidden?->visibility}\n";

    $described = $client->usersPlaylistsDescription($kind, 'Written by playlist_roundtrip.php');
    echo "describe -> {$described?->description}\n";

    $withTrack = $client->usersPlaylistsInsertTrack(
        $kind,
        new TrackId(id: TRACK_ID, albumId: ALBUM_ID),
    );
    echo "insert   -> {$withTrack?->trackCount} track(s), revision={$withTrack?->revision}\n";

    $emptied = $client->usersPlaylistsDeleteTrack($kind, 0, 1, $withTrack?->revision);
    echo "remove   -> {$emptied?->trackCount} track(s), revision={$emptied?->revision}\n";

    $recommendations = $client->usersPlaylistsRecommendations($kind);
    $suggested = null === $recommendations ? 0 : count($recommendations->tracks);
    echo "suggests -> {$suggested} track(s)\n";

    // A playlist made a moment ago has no trailer, and the API says so with a
    // 404 rather than an empty answer. That is the endpoint working, not a
    // failure, so it must not take the rest of the run down with it.
    try {
        $trailer = $client->usersPlaylistsTrailer($kind);
        echo 'trailer  -> '.(null === $trailer ? 'none' : 'shareable='.var_export($trailer->shareable, true))."\n";
    } catch (NotFoundException $e) {
        echo "trailer  -> none ({$e->getMessage()})\n";
    }

    // The reading methods, against the playlist we just made, so that one run
    // covers the whole domain rather than the writes alone.
    echo "\n-- reads --\n";

    $one = $client->usersPlaylists($kind);
    echo "by kind  -> {$one?->title}\n";

    $many = $client->usersPlaylistsMany([$kind]);
    echo 'by kinds -> '.count($many)." playlist(s)\n";

    $kinds = $client->usersPlaylistsKinds();
    echo 'kinds    -> '.count($kinds).' kind(s), ours '.(in_array($kind, $kinds, true) ? 'present' : 'MISSING')."\n";

    $ownerKind = $one?->ownerKind();

    if (null !== $ownerKind) {
        $byPair = $client->playlistsList([$ownerKind]);
        echo 'by pair  -> '.count($byPair)." playlist(s)\n";

        // Pairs here too, despite the parameter being named playlistIds.
        $batch = $client->playlists([$ownerKind]);
        echo 'batch    -> '.(null === $batch ? 0 : count($batch->playlists))." playlist(s)\n";
    }

    $uuid = $one?->playlistUuid;

    if (null === $uuid) {
        echo "by uuid  -> skipped, this playlist has no uuid\n";
    } else {
        $byUuid = $client->playlist($uuid);
        echo "by uuid  -> {$byUuid?->title}\n";

        // The uuid endpoints see a private playlist differently: reading it
        // works because we own it, but similar-entities answers
        // playlist-not-found. Make it public again before asking.
        $client->usersPlaylistsVisibility($kind, 'public');

        try {
            $similar = $client->playlistSimilarEntities($uuid);
            echo 'similar  -> '.(null === $similar ? 0 : count($similar->items))." item(s)\n";
        } catch (NotFoundException $e) {
            echo "similar  -> none ({$e->getMessage()})\n";
        }
    }

    try {
        $personal = $client->playlistsPersonal('playlistOfTheDay');
        echo 'personal -> '.(null === $personal ? 'none' : $personal->type.', ready='.var_export($personal->ready, true))."\n";
    } catch (NotFoundException $e) {
        echo "personal -> none ({$e->getMessage()})\n";
    }
} catch (YandexMusicException $e) {
    $failure = $e;
    echo "\nFailed partway through: {$e->getMessage()}\n";
}

$deleted = $client->usersPlaylistsDelete($kind);
echo "\ndelete   -> ".($deleted ? 'ok' : 'FAILED')."\n";

$after = count($client->usersPlaylistsList());
echo "playlists after: {$after}\n";

echo "\n".$unknownFields->summary();

if ($after !== $before) {
    echo "\nWARNING: the count did not come back to where it started.\n";
    echo "Look for a playlist named \"{$title}\" and remove it by hand.\n";

    exit(1);
}

exit(null === $failure ? 0 : 1);
