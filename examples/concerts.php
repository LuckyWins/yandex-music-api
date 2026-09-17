<?php

declare(strict_types=1);

/**
 * What is on: cities, the listing, an artist's dates, and one concert's page.
 *
 * Reads only, so it runs without asking. Pass an artist id as the first
 * argument and a city name as the second.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Support\Bootstrap;
use LuckyWins\YandexMusic\Examples\Support\UnknownFieldCollector;
use LuckyWins\YandexMusic\Exception\YandexMusicException;

$arguments = Bootstrap::arguments();
$artistId = $arguments[1] ?? '4611844';
$cityName = $arguments[2] ?? 'Москва';

$unknownFields = new UnknownFieldCollector();
$client = Bootstrap::authorizedClient($unknownFields);

echo "-- where --\n";

$locations = $client->concertsLocations();
$cities = null === $locations ? [] : $locations->locations;

echo '  '.count($cities)." city(ies)\n";

foreach (array_slice($cities, 0, 6) as $city) {
    echo '      '.$city->id.' '.$city->name."\n";
}

$cityId = $locations?->idOf($cityName);

echo "  {$cityName} -> ".(null === $cityId ? 'not listed' : (string) $cityId)."\n";

echo "\n-- how the tab is laid out --\n";

$config = $client->concertsTabConfig()?->config;
$top = $config?->top;
$below = $config?->feed;

printf(
    "  top  offset=%s limit=%s\n  feed offset=%s limit=%s\n",
    null === $top ? '—' : $top->offset,
    null === $top ? '—' : $top->limit,
    null === $below ? '—' : $below->offset,
    null === $below ? '—' : $below->limit,
);

echo "\n-- what is on --\n";

$everywhere = $client->concertsFeed();

echo '  everywhere: '.count(null === $everywhere ? [] : $everywhere->concerts())." concert(s)\n";

$feed = null === $cityId ? $everywhere : $client->concertsFeed([$cityId]);
$concerts = null === $feed ? [] : $feed->concerts();

echo "  {$cityName}: ".count($concerts)." concert(s)\n";

foreach (array_slice($concerts, 0, 5) as $concert) {
    $price = $concert->minPrice;

    printf(
        "      %-28s %-16s %s%s\n",
        mb_substr($concert->concertTitle ?? '?', 0, 28),
        $concert->datetime ?? '?',
        $concert->place ?? '?',
        null === $price || null === $price->value ? '' : ', from '.$price->value.' '.($price->currency ?? ''),
    );
}

// An entry of a kind the library does not know keeps its type and drops its
// data; this says whether that ever happens.
$unknownKinds = [];

foreach (null === $feed ? [] : $feed->items as $item) {
    if (null === $item->data && null !== $item->type) {
        $unknownKinds[$item->type] = true;
    }
}

if ([] !== $unknownKinds) {
    echo '  entries of kinds not modelled: '.implode(', ', array_keys($unknownKinds))."\n";
}

echo "\n-- the artist --\n";

$artistConcerts = $client->artistsConcerts($artistId);
$dates = null === $artistConcerts ? [] : $artistConcerts->concerts;

echo '  '.(null === $artistConcerts ? '?' : $artistConcerts->artistTitle).': '.count($dates)." date(s)\n";

foreach (array_slice($dates, 0, 5) as $concert) {
    printf("      %-18s %-22s %s\n", $concert->city ?? '?', $concert->datetime ?? '?', $concert->place ?? '?');
}

// What brief-info puts in its concerts field has never been checked; the
// reference does not model it at all.
$brief = $client->artistsBriefInfo($artistId);
$briefConcerts = null === $brief ? [] : $brief->concerts;

echo '  brief-info concerts: '.count($briefConcerts)." entry(ies)\n";

if ([] !== $briefConcerts && is_array($briefConcerts[0])) {
    echo '      keys: '.implode(', ', array_keys($briefConcerts[0]))."\n";
}

echo "\n-- one concert --\n";

$first = $dates[0] ?? $concerts[0] ?? null;

if (null === $first || null === $first->id) {
    echo "  nothing to open\n";
} else {
    try {
        $info = $client->concertInfo($first->id);

        $concert = $info?->concert;
        $description = $info?->description;
        $text = null === $description ? null : $description->text;

        printf(
            "  %s\n      %s, %s\n      description: %s\n      lead artist: %s\n",
            null === $concert ? '?' : $concert->concertTitle,
            null === $concert ? '?' : $concert->city,
            null === $concert ? '?' : $concert->place,
            null === $text ? 'none' : mb_substr($text, 0, 50).'…',
            null === $info || null === $info->leadArtistId ? '—' : $info->leadArtistId,
        );
    } catch (YandexMusicException $e) {
        echo '  info: '.$e->getMessage()."\n";
    }

    try {
        $skeleton = $client->concertSkeleton($first->id);
        $types = array_map(static fn ($block): string => $block->type ?? '?', null === $skeleton ? [] : $skeleton->blocks);

        echo '  skeleton: '.count($types).' block(s)'.([] === $types ? '' : ': '.implode(', ', $types))."\n";
    } catch (YandexMusicException $e) {
        echo '  skeleton: '.$e->getMessage()."\n";
    }
}

echo "\n".$unknownFields->summary();
