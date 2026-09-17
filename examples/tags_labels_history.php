<?php

declare(strict_types=1);

/**
 * Tags, labels and what the account has been listening to.
 *
 * Reads only — the one POST here asks the history to fill entries in, which
 * changes nothing — so it runs without confirmation.
 *
 * Pass a tag as the first argument and a label id as the second.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Support\Bootstrap;
use LuckyWins\YandexMusic\Examples\Support\UnknownFieldCollector;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryQuery;

$arguments = Bootstrap::arguments();

$unknownFields = new UnknownFieldCollector();
$client = Bootstrap::authorizedClient($unknownFields)->init();

echo "-- how the catalogue is tagged --\n";

$tags = $client->metatags();
$trees = null === $tags ? [] : $tags->trees;
$all = null === $tags ? [] : $tags->tags();

echo '  '.count($trees).' tree(s), '.count($all)." tag(s) in all\n";

foreach (array_slice($trees, 0, 4) as $tree) {
    echo '      '.($tree->navigationId ?? '?').' — '.($tree->title ?? '?').', '.count($tree->leaves)." branch(es)\n";
}

$tagId = $arguments[1] ?? ($all[0]->tag ?? null);

if (null === $tagId) {
    echo "  no tag to open\n";

    exit(1);
}

echo "\n-- the tag {$tagId} --\n";

try {
    $tag = $client->metatag($tagId, tracksCount: 3, artistsCount: 3, albumsCount: 3, playlistsCount: 3);

    if (null === $tag) {
        echo "  nothing came back\n";
    } else {
        $title = $tag->title;
        $named = null === $title ? null : ($title->fullTitle ?? $title->title);

        printf(
            "  %s: %d artist(s), %d album(s), %d playlist(s), station %s\n",
            $named ?? $tagId,
            count($tag->artists),
            count($tag->albums),
            count($tag->playlists),
            $tag->stationId ?? '—',
        );

        $sorts = array_map(
            static fn ($value): string => ($value->value ?? '?').(true === $value->active ? '*' : ''),
            $tag->albumsSortByValues,
        );

        echo '      albums can be sorted by: '.([] === $sorts ? 'unsaid' : implode(', ', $sorts))."\n";
    }

    $albums = $client->metatagAlbums($tagId, 0, 5);
    $albumPager = $albums?->pager;
    echo '  albums page: '.count(null === $albums ? [] : $albums->albums)
        .' of '.(null === $albumPager ? '?' : $albumPager->total)."\n";

    $artists = $client->metatagArtists($tagId, offset: 0, limit: 5, tracksPerArtist: 2);
    echo '  artists page: '.count(null === $artists ? [] : $artists->artists)." entry(ies)\n";

    $playlists = $client->metatagPlaylists($tagId, 0, 5);
    echo '  playlists page: '.count(null === $playlists ? [] : $playlists->playlists)."\n";
} catch (YandexMusicException $e) {
    echo '  '.$e->getMessage()."\n";
}

echo "\n-- a label --\n";

// Labels are found through an album rather than listed, so take one from a
// release if no id was given.
$labelId = $arguments[2] ?? null;

if (null === $labelId) {
    $album = $client->artistsDirectAlbums(4611844, 0, 1)?->albums[0] ?? null;
    $fullAlbum = null === $album || null === $album->id ? null : $client->album($album->id);

    foreach (null === $fullAlbum ? [] : $fullAlbum->labels as $label) {
        if (is_object($label) && null !== $label->id) {
            $labelId = (string) $label->id;

            break;
        }
    }
}

if (null === $labelId) {
    echo "  no label to open — pass one as the second argument\n";
} else {
    try {
        $label = $client->label($labelId);

        echo '  '.(null === $label ? '?' : $label->name).' (id '.$labelId.")\n";

        $releases = $client->labelAlbums($labelId, 0, 5);
        $releasePager = $releases?->pager;
        echo '      releases: '.count(null === $releases ? [] : $releases->albums)
            .' of '.(null === $releasePager ? '?' : $releasePager->total)."\n";

        $signed = $client->labelArtists($labelId, 0, 5);
        $signedPager = $signed?->pager;
        echo '      artists:  '.count(null === $signed ? [] : $signed->artists)
            .' of '.(null === $signedPager ? '?' : $signedPager->total)."\n";
    } catch (YandexMusicException $e) {
        echo '  '.$e->getMessage()."\n";
    }
}

echo "\n-- what was played --\n";

$history = $client->musicHistory(5);
$days = null === $history ? [] : $history->historyTabs;

echo '  '.count($days)." day(s)\n";

$query = new MusicHistoryQuery();
$asked = 0;

foreach (array_slice($days, 0, 3) as $day) {
    printf("      %-12s %d stretch(es)\n", $day->date ?? '?', count($day->items));

    foreach (array_slice($day->items, 0, 2) as $group) {
        $source = $group->context;
        $context = $source?->context();
        $subject = $context?->subject();

        printf(
            "          from %-10s %s, %d track(s)\n",
            null === $source ? '?' : $source->type,
            null === $subject ? 'not filled in' : (new ReflectionClass($subject))->getShortName(),
            count($group->tracks),
        );

        // Ask the history to fill in one of these by reference, which is what
        // musicHistoryItems() is for.
        $first = $group->tracks[0] ?? null;
        $id = null === $first ? null : $first->data?->itemId;

        if (null !== $id && null !== $id->trackId && null !== $id->albumId && $asked < 3) {
            $query->track($id->trackId, $id->albumId);
            ++$asked;
        }
    }
}

if ($query->isEmpty()) {
    echo "  nothing to ask about\n";
} else {
    $items = $client->musicHistoryItems($query);
    $filled = 0;

    foreach (null === $items ? [] : $items->items as $item) {
        if (null !== $item->track()) {
            ++$filled;
        }
    }

    echo "  asked about {$asked} track(s), filled in {$filled}\n";
}

echo "\n".$unknownFields->summary();
