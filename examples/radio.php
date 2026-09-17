<?php

declare(strict_types=1);

/**
 * Listen to a radio station for exactly one track.
 *
 * Reading is harmless, but the feedback is not: it feeds your recommendations
 * and your listening history, and it cannot be taken back. So this asks for a
 * station's tracks, reports the four events for the first one, and stops. One
 * play lands in your history.
 *
 * It also prints what each station setting accepts — the service advertises
 * that in every station's restrictions, which is where this library's enums
 * come from.
 *
 * Pass a station as the first argument, such as `user:onyourwave`.
 * Asks for confirmation; pass --yes to skip the prompt.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Bootstrap;
use LuckyWins\YandexMusic\Examples\UnknownFieldCollector;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\Rotor\DiscreteScale as Scale;
use LuckyWins\YandexMusic\Model\Rotor\Enum as Vocabulary;
use LuckyWins\YandexMusic\Model\Rotor\StationResult;

$station = $argv[1] ?? 'genre:allrock';
$confirmed = in_array('--yes', $argv, true);

if (!$confirmed) {
    if (!stream_isatty(STDIN)) {
        echo "Refusing to send feedback without confirmation.\n";
        echo "Re-run with --yes if you really mean it.\n";

        exit(1);
    }

    echo "This reports one played track to {$station}, which nudges your\n";
    echo "recommendations and adds a play to your history. It cannot be undone.\n";
    echo 'Type "run" to continue: ';

    $answer = fgets(STDIN);

    if (!is_string($answer) || 'run' !== trim($answer)) {
        echo "Cancelled. Nothing was sent.\n";

        exit(0);
    }
}

$unknownFields = new UnknownFieldCollector();
$client = Bootstrap::authorizedClient($unknownFields)->init();

echo "\n-- account --\n";

$status = $client->rotorAccountStatus();
echo 'skips per hour: '.(null === $status ? '—' : $status->skipsPerHour)."\n";

echo "\n-- dashboard --\n";

$dashboard = $client->rotorStationsDashboard();
$offered = null === $dashboard ? [] : $dashboard->stations;

echo 'id: '.(null === $dashboard ? '—' : $dashboard->dashboardId).', stations: '.count($offered)."\n";

foreach (array_slice($offered, 0, 5) as $result) {
    $named = $result->station;
    echo '  '.($named?->id?->tag() ?? '?').' — '.(null === $named ? '?' : $named->name)."\n";
}

echo "\n-- all stations --\n";

$stations = $client->rotorStationsList();
echo count($stations)." station(s)\n";

echo "\n-- {$station} --\n";

$info = $client->rotorStationInfo($station)[0] ?? null;

if (!$info instanceof StationResult) {
    echo "no such station\n";

    exit(1);
}

$tuned = $info->settings;

$named = $info->station;

echo 'name: '.(null === $named ? '?' : $named->name)."\n";
echo 'tuned to: language='.(null === $tuned ? '—' : $tuned->language)
    .' diversity='.(null === $tuned ? '—' : $tuned->diversity)
    .' moodEnergy='.(null === $tuned ? '—' : $tuned->moodEnergy)."\n";

// This is where the enum cases come from: the service says what it accepts.
echo "\n-- what the settings accept --\n";

$restrictions = $info->station?->restrictions;

foreach (['language', 'diversity', 'moodEnergy'] as $name) {
    $vocabulary = $restrictions?->{$name};

    if ($vocabulary instanceof Vocabulary) {
        echo sprintf("  %-11s %s\n", $name, implode(', ', $vocabulary->values()));
    } else {
        echo sprintf("  %-11s —\n", $name);
    }
}

foreach (['mood', 'energy'] as $name) {
    $scale = $restrictions?->{$name};
    $min = $scale instanceof Scale ? $scale->min : null;
    $max = $scale instanceof Scale ? $scale->max : null;

    echo sprintf(
        "  %-11s %s..%s\n",
        $name,
        null === $min ? '—' : $min->value,
        null === $max ? '—' : $max->value,
    );
}

echo "\n-- tracks --\n";

$batch = $client->rotorStationTracks($station);
$tracks = null === $batch ? [] : $batch->tracks();

echo 'batch '.(null === $batch ? '—' : $batch->batchId).', '.count($tracks)." track(s)\n";

foreach (array_slice($tracks, 0, 5) as $track) {
    echo '  '.$track->id.' — '.(null === $track->title ? '?' : $track->title)."\n";
}

$first = $tracks[0] ?? null;

if (null === $first) {
    echo "\nnothing to report feedback for\n";

    exit(1);
}

echo "\n-- feedback for one track --\n";

$batchId = null === $batch ? null : $batch->batchId;
$played = min(30.0, (null === $first->durationMs ? 30000 : $first->durationMs) / 1000);

// Where playback is coming from. The service refuses radioStarted without it.
$from = 'mobile-radio-'.str_replace(':', '-', $station);

/**
 * Report one event and say what happened, without letting a refusal hide the
 * events that come after it.
 *
 * @param callable(): bool $send
 */
function report(string $label, callable $send): void
{
    try {
        printf("  %-13s -> %s\n", $label, $send() ? 'ok' : 'refused');
    } catch (YandexMusicException $e) {
        printf("  %-13s -> %s\n", $label, $e->getMessage());
    }
}

report('radioStarted', static fn (): bool => $client->rotorStationFeedbackRadioStarted($station, $from, $batchId));
report('trackStarted', static fn (): bool => $client->rotorStationFeedbackTrackStarted($station, $first->id, $batchId));
report(
    'trackFinished',
    static fn (): bool => $client->rotorStationFeedbackTrackFinished($station, $first->id, $played, $batchId),
);
report('skip', static fn (): bool => $client->rotorStationFeedbackSkip($station, $first->id, 1.0, $batchId));

echo "\n".$unknownFields->summary();
