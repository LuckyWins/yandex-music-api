<?php

declare(strict_types=1);

/**
 * Pin things, put them back, hand over a queue, and presave an album.
 *
 * Everything this sets, it unsets before it finishes: the pins go back to what
 * they were, the presave is removed, and the queue is left behind because
 * there is no endpoint to delete one — it is a record of what a device was
 * playing, and a stale one is harmless.
 *
 * Asks for confirmation. Pass --yes to skip the prompt.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Examples\Bootstrap;
use LuckyWins\YandexMusic\Examples\UnknownFieldCollector;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Queue\Context;
use LuckyWins\YandexMusic\Model\Queue\Queue;

$arguments = Bootstrap::arguments();
$confirmed = in_array('--yes', $arguments, true);

if (!$confirmed) {
    if (!stream_isatty(STDIN)) {
        echo "Refusing to write to the account without confirmation.\n";
        echo "Re-run with --yes if you really mean it.\n";

        exit(1);
    }

    echo "This pins an album, an artist and a playlist, then unpins them;\n";
    echo "presaves an album and removes it; and creates one playback queue,\n";
    echo "which cannot be deleted afterwards and is harmless.\n";
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
 * Say what a call did, without a refusal stopping the rest.
 *
 * Named apart from radio.php's helper: the examples are analysed together,
 * and two global functions of one name are one too many.
 *
 * @param callable(): string $act
 */
function announce(string $label, callable $act): void
{
    try {
        printf("  %-16s %s\n", $label, $act());
    } catch (YandexMusicException $e) {
        printf("  %-16s %s\n", $label, $e->getMessage());
    }
}

/** @return list<string> */
function pinned(Client $client): array
{
    $pins = $client->pins();
    $shown = [];

    foreach (null === $pins ? [] : $pins->pins as $pin) {
        $data = $pin->data;
        $what = match (true) {
            null === $data => '?',
            null !== $data->kind => $data->uid.':'.$data->kind,
            null !== $data->id => (string) $data->id,
            default => 'wave',
        };

        $shown[] = $pin->type.'/'.$what;
    }

    return $shown;
}

$before = pinned($client);

echo "\npinned at the start: ".([] === $before ? 'nothing' : implode(', ', $before))."\n";

// Objects taken from the API rather than written down: an id that no longer
// exists is accepted and then quietly ignored, which looks like a broken
// method. That lesson cost a stage.
$album = $client->artistsDirectAlbums(4611844, 0, 1)?->albums[0] ?? null;
$playlist = $client->usersPlaylistsList()[0] ?? null;

echo "\n-- pinning --\n";

if (null !== $album && null !== $album->id) {
    announce('album', static fn (): string => null === $client->pinAlbum($album->id) ? 'refused' : 'ok');
}

announce('artist', static fn (): string => null === $client->pinArtist(4611844) ? 'refused' : 'ok');

if (null !== $playlist && null !== $playlist->uid && null !== $playlist->kind) {
    announce(
        'playlist',
        static fn (): string => null === $client->pinPlaylist($playlist->uid, $playlist->kind) ? 'refused' : 'ok',
    );
}

// What a wave wants here is a guess worth checking: the seeds of a station.
announce('wave', static fn (): string => null === $client->pinWave('user:onyourwave') ? 'refused' : 'ok');

$during = pinned($client);

echo '  now pinned:     '.([] === $during ? 'nothing' : implode(', ', $during))."\n";

echo "\n-- unpinning --\n";

if (null !== $album && null !== $album->id) {
    announce('album', static fn (): string => $client->unpinAlbum($album->id) ? 'ok' : 'refused');
}

announce('artist', static fn (): string => $client->unpinArtist(4611844) ? 'ok' : 'refused');

if (null !== $playlist && null !== $playlist->uid && null !== $playlist->kind) {
    announce('playlist', static fn (): string => $client->unpinPlaylist($playlist->uid, $playlist->kind) ? 'ok' : 'refused');
}

announce('wave', static fn (): string => $client->unpinWave('user:onyourwave') ? 'ok' : 'refused');

$after = pinned($client);

echo '  pinned at end:  '.([] === $after ? 'nothing' : implode(', ', $after))."\n";
echo '  back as it was: '.($after === $before ? 'yes' : 'NO')."\n";

echo "\n-- queues --\n";

announce('list', static fn (): string => count($client->queuesList()).' queue(s)');

$batch = $client->artistsTracks(4611844, 0, 3);
$tracks = null === $batch ? [] : $batch->tracks;
$ids = [];

foreach ($tracks as $track) {
    $ids[] = new TrackId(id: (int) $track->id, albumId: (int) ($track->albums[0]->id ?? 0));
}

if ([] === $ids) {
    echo "  create           no tracks to put in a queue\n";
} else {
    $queueId = null;

    announce('create', static function () use ($client, $ids, &$queueId): string {
        $queueId = $client->queueCreate(new Queue(
            new Context('artist', '4611844', 'Miyagi & Эндшпиль'),
            $ids,
            currentIndex: 0,
            from: 'api-probe',
        ));

        return $queueId ?? 'refused';
    });

    if (is_string($queueId)) {
        announce('read back', static function () use ($client, $queueId): string {
            $queue = $client->queue($queueId);

            if (null === $queue) {
                return 'nothing';
            }

            return count($queue->tracks).' track(s), playing #'.($queue->currentIndex ?? '?');
        });

        announce('move position', static fn (): string => $client->queueUpdatePosition($queueId, 1) ? 'ok' : 'refused');
    }
}

echo "\n-- presaves --\n";

announce('current', static function () use ($client): string {
    $presaves = $client->usersPresaves();

    if (null === $presaves) {
        return 'nothing';
    }

    return count($presaves->upcomingAlbums).' upcoming, '.count($presaves->releasedAlbums).' released';
});

if (null !== $album && null !== $album->id) {
    announce('add', static fn (): string => $client->usersPresavesAdd($album->id) ? 'ok' : 'refused');
    announce('remove', static fn (): string => $client->usersPresavesRemove($album->id) ? 'ok' : 'refused');
}

echo "\n".$unknownFields->summary();

exit($after === $before ? 0 : 1);
