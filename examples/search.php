<?php

declare(strict_types=1);

/**
 * Search the catalogue and print what came back.
 *
 * Reads only: nothing here changes anything on the account, so it runs without
 * asking. Pass a query of your own as the first argument.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Bootstrap;
use LuckyWins\YandexMusic\Examples\UnknownFieldCollector;
use LuckyWins\YandexMusic\Model\Search\Search;
use LuckyWins\YandexMusic\Model\Search\SearchType;

$arguments = Bootstrap::arguments();
$query = $arguments[1] ?? 'нирвана';

$unknownFields = new UnknownFieldCollector();
$client = Bootstrap::authorizedClient($unknownFields);

/**
 * One line per result set: what it holds, how much of it there is, and whether
 * the set's own type agrees with the field it arrived in.
 *
 * The disagreement is the interesting part: the service has been seen filing
 * playlists under artists, and the models follow the type rather than the
 * field precisely because reading one as the other would throw.
 */
function describeSets(Search $search): void
{
    foreach ($search->sets() as $field => $set) {
        // Field names are plural and camelCase, types are singular and
        // snake_case: podcastEpisodes holds podcast_episode. Compare them with
        // both spellings flattened, or every set looks like a mismatch.
        $flatten = static fn (string $name): string => strtolower(str_replace('_', '', $name));
        $agrees = null === $set->type || $flatten($set->type) === $flatten(rtrim($field, 's'));

        printf(
            "  %-16s %3d of %-5s type=%-16s %s\n",
            $field,
            count($set->results),
            $set->total ?? '?',
            $set->type ?? '—',
            $agrees ? '' : '<- does not match the field it arrived in',
        );
    }
}

foreach ([SearchType::All, SearchType::Track, SearchType::Podcast] as $type) {
    echo "\n== {$query} / {$type->value} ==\n";

    $search = $client->search($query, type: $type);

    if (null === $search) {
        echo "  nothing came back\n";

        continue;
    }

    printf(
        "  request=%s page=%s perPage=%s\n",
        $search->searchRequestId ?? '—',
        $search->page ?? '—',
        $search->perPage ?? '—',
    );

    if (true === $search->misspellCorrected) {
        echo "  corrected \"{$search->misspellOriginal}\" to \"{$search->misspellResult}\"\n";
    }

    $best = $search->best;

    if (null !== $best) {
        echo '  best: '.($best->type ?? '?').' -> '.(null === $best->result ? 'not modelled' : $best->result::class)."\n";
    }

    describeSets($search);
}

echo "\n== suggestions for \"".mb_substr($query, 0, 4)."\" ==\n";

$suggestions = $client->searchSuggest(mb_substr($query, 0, 4));

foreach (null === $suggestions ? [] : $suggestions->suggestions as $suggestion) {
    echo "  {$suggestion}\n";
}

echo "\n".$unknownFields->summary();
