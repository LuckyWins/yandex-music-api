<?php

declare(strict_types=1);

/**
 * Everything the service will say about one artist.
 *
 * Reads only, so it runs without asking. Pass an artist id as the first
 * argument; the default is a duo that has been on the service for years.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Bootstrap;
use LuckyWins\YandexMusic\Examples\UnknownFieldCollector;
use LuckyWins\YandexMusic\Exception\YandexMusicException;

$arguments = Bootstrap::arguments();
$artistId = $arguments[1] ?? '4611844';

$unknownFields = new UnknownFieldCollector();
$client = Bootstrap::authorizedClient($unknownFields);

/**
 * Run one read and say what came back, without letting an endpoint that has
 * nothing for this artist stop the rest.
 *
 * @param callable(): string $describe
 */
function show(string $label, callable $describe): void
{
    try {
        printf("  %-14s %s\n", $label, $describe());
    } catch (YandexMusicException $e) {
        printf("  %-14s %s\n", $label, $e->getMessage());
    }
}

echo "-- who --\n";

show('info', static function () use ($client, $artistId): string {
    $info = $client->artistsInfo($artistId);

    if (null === $info) {
        return 'nothing';
    }

    $artist = $info->artist;
    $stats = $info->stats;

    return sprintf(
        '%s, %s listeners last month, trailer=%s',
        null === $artist ? '?' : $artist->name,
        null === $stats ? '?' : $stats->lastMonthListeners,
        var_export($info->trailer?->available, true),
    );
});

show('about', static function () use ($client, $artistId): string {
    $about = $client->artistsAbout($artistId);

    if (null === $about) {
        return 'nothing';
    }

    return sprintf(
        '%d link(s), %d cover(s), description %s',
        count($about->links),
        count($about->covers),
        null === $about->description ? 'absent' : mb_substr($about->description, 0, 40).'…',
    );
});

show('links', static function () use ($client, $artistId): string {
    $links = $client->artistsLinks($artistId);
    $titles = array_map(static fn ($link): string => $link->title ?? '?', null === $links ? [] : $links->links);

    return [] === $titles ? 'none' : implode(', ', $titles);
});

echo "\n-- what they made --\n";

show('discography', static function () use ($client, $artistId): string {
    $albums = $client->artistsDiscographyAlbums($artistId, 0, 5);

    if (null === $albums) {
        return 'nothing';
    }

    $pager = $albums->pager;

    return count($albums->albums).' of '.(null === $pager ? '?' : $pager->total);
});

show('safe albums', static function () use ($client, $artistId): string {
    $albums = $client->artistsSafeDirectAlbums($artistId, 0, 5);

    if (null === $albums) {
        return 'nothing';
    }

    $pager = $albums->pager;

    return count($albums->albums).' of '.(null === $pager ? '?' : $pager->total);
});

show('track ids', static fn (): string => count($client->artistsTrackIds($artistId)).' id(s)');

show('clips', static function () use ($client, $artistId): string {
    $clips = $client->artistsClips($artistId);

    return null === $clips ? 'nothing' : count($clips->clips()).' clip(s)';
});

echo "\n-- the rest --\n";

show('trailer', static function () use ($client, $artistId): string {
    $trailer = $client->artistsTrailer($artistId);

    if (null === $trailer) {
        return 'nothing';
    }

    $info = $trailer->trailer;

    if (null === $info) {
        return 'no trailer';
    }

    return (null === $info->title ? 'untitled' : $info->title).', '.count($info->tracks).' track(s)';
});

show('donation', static function () use ($client, $artistId): string {
    $donations = $client->artistsDonation($artistId);

    return null === $donations ? 'nothing' : count($donations->donations).' way(s) to support';
});

show('disclaimer', static fn (): string => count($client->artistsDisclaimer($artistId)).' notice(s)');

// The layout endpoint needs a second id, and what it accepts is worth seeing.
echo "\n-- layout --\n";

foreach (['artist-page', 'artist', 'default'] as $skeletonId) {
    show($skeletonId, static function () use ($client, $artistId, $skeletonId): string {
        $skeleton = $client->artistsSkeleton($artistId, $skeletonId);

        if (null === $skeleton) {
            return 'nothing';
        }

        $types = array_map(static fn ($block): string => $block->type ?? '?', $skeleton->blocks);

        return count($skeleton->blocks).' block(s): '.implode(', ', array_slice($types, 0, 6));
    });
}

echo "\n-- albums and clips --\n";

$album = $client->artistsDirectAlbums($artistId, 0, 1)?->albums[0] ?? null;

if (null === $album || null === $album->id) {
    echo "  no album to ask about\n";
} else {
    show('album trailer', static function () use ($client, $album): string {
        $trailer = $client->albumsTrailer((string) $album->id);

        $info = $trailer?->trailer;

        return null === $info ? 'nothing' : (null === $info->title ? 'untitled' : $info->title);
    });

    show('album similar', static function () use ($client, $album): string {
        $similar = $client->albumsSimilarEntities((string) $album->id);

        return null === $similar ? 'nothing' : count($similar->items).' item(s)';
    });
}

$clip = $client->clipsWillLike(0, 1)?->clips[0] ?? null;

if (null === $clip || null === $clip->clipId) {
    echo "  no clip to ask about\n";
} else {
    show('clip credits', static function () use ($client, $clip): string {
        $credits = $client->clipsCredits($clip->clipId);

        return null === $credits ? 'nothing' : 'ok';
    });

    show('clip notices', static fn (): string => count($client->clipsDisclaimer($clip->clipId)).' notice(s)');
}

echo "\n".$unknownFields->summary();
