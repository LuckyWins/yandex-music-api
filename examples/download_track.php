<?php

declare(strict_types=1);

/**
 * Download a track.
 *
 * Usage:
 *   php examples/download_track.php <trackId> [output.mp3]
 *
 * Finds the highest bitrate on offer, resolves the manifest into a URL and
 * streams the audio to disk. Nothing is held in memory beyond a chunk at a
 * time, so this works the same for a three-minute single as for an hour-long
 * mix.
 *
 * Two things routinely go wrong here and neither is obvious from the error:
 *
 * A manifest is good for roughly a minute. Fetching the download info, going
 * away, and resolving it later gets you a 410 rather than audio.
 *
 * The audio does not come from the API host. If your network reaches
 * api.music.yandex.net but not Yandex's storage and streaming hosts — a
 * corporate VPN will often do exactly that — the request hangs instead of
 * failing cleanly. See the README on unreachable networks.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Bootstrap;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\Track\DownloadInfo;

$trackId = $argv[1] ?? null;

if (null === $trackId) {
    echo "Usage: php examples/download_track.php <trackId> [output.mp3]\n";
    echo "The track id is the last number in a music.yandex.ru track URL.\n";

    exit(1);
}

try {
    $client = Bootstrap::authorizedClient();

    $tracks = $client->tracks($trackId);
    $track = $tracks[0] ?? null;

    if (null === $track) {
        echo "No such track: {$trackId}\n";

        exit(1);
    }

    $name = implode(', ', $track->artistNames()).' — '.($track->title ?? 'untitled');
    echo "{$name}\n";

    $variants = $client->tracksDownloadInfo($trackId);

    if ([] === $variants) {
        echo "Nothing on offer for this track. A subscription is usually what is missing.\n";

        exit(1);
    }

    // Best available, rather than whatever came first.
    usort($variants, static fn (DownloadInfo $a, DownloadInfo $b): int => $b->bitrateInKbps <=> $a->bitrateInKbps);
    $best = $variants[0];

    printf("available: %s\n", implode(', ', array_map(
        static fn (DownloadInfo $i): string => "{$i->codec} {$i->bitrateInKbps}",
        $variants,
    )));
    printf("taking:    %s %d kbps\n", $best->codec, $best->bitrateInKbps);

    $path = $argv[2] ?? sprintf('%s.%s', $trackId, $best->codec);

    $written = $best->download($path);

    printf("\nwrote %s (%s)\n", $path, formatBytes($written));
} catch (YandexMusicException $e) {
    echo "\nFailed: {$e->getMessage()}\n";

    exit(1);
}

function formatBytes(int $bytes): string
{
    if ($bytes < 1024 * 1024) {
        return sprintf('%.1f KiB', $bytes / 1024);
    }

    return sprintf('%.1f MiB', $bytes / 1024 / 1024);
}
