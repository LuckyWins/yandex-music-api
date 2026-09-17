<?php

declare(strict_types=1);

/**
 * Read the front page: its blocks, the chart, what is new, the feed and the
 * genres.
 *
 * Reads only, so it runs without asking.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Bootstrap;
use LuckyWins\YandexMusic\Examples\UnknownFieldCollector;
use LuckyWins\YandexMusic\Model\Landing\BlockType;

$unknownFields = new UnknownFieldCollector();
$client = Bootstrap::authorizedClient($unknownFields)->init();

echo "-- landing blocks --\n";

$landing = $client->landing([
    BlockType::PersonalPlaylists,
    BlockType::Promotions,
    BlockType::NewReleases,
    BlockType::NewPlaylists,
    BlockType::Mixes,
    BlockType::Chart,
    BlockType::PlayContexts,
]);

foreach (null === $landing ? [] : $landing->blocks as $block) {
    $data = null === $block->data ? '' : ' data='.(new ReflectionClass($block->data))->getShortName();

    printf("  %-20s %2d entity(ies)%s  %s\n", $block->type, count($block->entities), $data, $block->title);

    $kinds = [];

    foreach ($block->entities as $entity) {
        $kind = $entity->type ?? '?';
        $kinds[$kind] = ($kinds[$kind] ?? 0) + 1;

        if (null === $entity->data && null !== $entity->type) {
            $kinds[$kind.' (not modelled)'] = ($kinds[$kind.' (not modelled)'] ?? 0) + 1;
        }
    }

    foreach ($kinds as $kind => $count) {
        echo "      {$count} × {$kind}\n";
    }
}

// The reference library pins a stranger's user id into every landing request.
// This is the check of whether it changes anything.
echo "\n-- does eitherUserId matter? --\n";

$blocks = 'personalplaylists,chart';
$without = $client->request->get($client->getBaseUrl().'/landing3', ['blocks' => $blocks]);
$with = $client->request->get($client->getBaseUrl().'/landing3', [
    'blocks' => $blocks,
    'eitherUserId' => '10254713668400548221',
]);

function blockTypes(mixed $answer): string
{
    if (!is_array($answer) || !is_array($answer['blocks'] ?? null)) {
        return '—';
    }

    $types = [];

    foreach ($answer['blocks'] as $block) {
        $type = is_array($block) ? ($block['type'] ?? null) : null;
        $types[] = is_string($type) ? $type : '?';
    }

    return implode(', ', $types);
}

echo '  without: '.blockTypes($without)."\n";
echo '  with:    '.blockTypes($with)."\n";

echo "\n-- chart --\n";

$chart = $client->chart();
$playlist = $chart?->chart;
$tracks = null === $playlist ? [] : $playlist->tracks;

echo '  '.(null === $chart ? '—' : $chart->title).': '.count($tracks)." track(s)\n";

foreach (null === $chart?->menu ? [] : $chart->menu->items as $item) {
    echo '      '.($item->selected ? '*' : ' ').' '.$item->title.' ('.$item->url.")\n";
}

foreach (array_slice($tracks, 0, 5) as $position) {
    $track = $position->track;
    $standing = $position->chart;

    printf(
        "  %2s. %s  (%s)\n",
        null === $standing ? '?' : $standing->position,
        null === $track ? '?' : $track->title,
        null === $standing ? '?' : $standing->progress,
    );
}

echo "\n-- what is new --\n";

$releases = $client->newReleases();
$playlists = $client->newPlaylists();
$podcasts = $client->podcasts();

echo '  releases:  '.count(null === $releases ? [] : $releases->newReleases)."\n";
echo '  playlists: '.count(null === $playlists ? [] : $playlists->newPlaylists)."\n";
echo '  podcasts:  '.count(null === $podcasts ? [] : $podcasts->podcasts)."\n";

echo "\n-- feed --\n";

$feed = $client->feed();

echo '  wizard passed: '.var_export($client->feedWizardIsPassed(), true)."\n";
echo '  generated: '.count(null === $feed ? [] : $feed->generatedPlaylists)." playlist(s)\n";
echo '  today: '.(null === $feed ? '—' : $feed->today)."\n";

foreach (array_slice(null === $feed ? [] : $feed->days, 0, 3) as $day) {
    printf("  %s: %d event(s), %d track(s) to play\n", $day->day, count($day->events), count($day->tracksToPlay));

    foreach (array_slice($day->events, 0, 3) as $event) {
        printf("      %-22s %s\n", $event->type, $event->title ?? '');
    }
}

echo "\n-- genres --\n";

$genres = $client->genres();

echo '  '.count($genres)." genre(s)\n";

foreach (array_slice($genres, 0, 5) as $genre) {
    printf(
        "      %-14s %-22s en=%s sub=%d\n",
        $genre->id,
        $genre->title,
        $genre->titleIn('en') ?? '—',
        count($genre->subGenres),
    );
}

echo "\n".$unknownFields->summary();
