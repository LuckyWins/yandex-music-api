<?php

declare(strict_types=1);

/**
 * Compare this library's client methods against the Python reference.
 *
 * The count of what is and is not ported has been arrived at by hand all
 * along, which is a poor way to keep a claim true. This does it from the
 * source: the reference's `def` names against this Client's public methods.
 *
 * It is a script rather than a test because CI has no checkout of the
 * reference next to it. Without one, this exits quietly and says so.
 *
 * Usage: php tools/compare-with-reference.php [path-to-reference]
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Client;

$arguments = $_SERVER['argv'] ?? [];
$given = is_array($arguments) && isset($arguments[1]) && is_string($arguments[1]) ? $arguments[1] : null;
$reference = $given ?? __DIR__.'/../../python-yandex-music-api';
$clientDir = $reference.'/yandex_music/_client';

if (!is_dir($clientDir)) {
    echo "No reference checkout at {$reference} — nothing to compare against.\n";
    echo "Clone MarshalX/yandex-music-api beside this repository, or pass its path.\n";

    exit(0);
}

/**
 * The reference's public client methods, as camelCase.
 *
 * `wrapper` is the decorator in _client/__init__.py, not an endpoint.
 * `rotor_station_settings2` is this library's rotorStationSettings: the
 * reference names it for a version while posting to /settings3.
 *
 * @return array<string, string> camelCase name => the Python name
 */
function referenceMethods(string $directory): array
{
    $renamed = ['rotor_station_settings2' => 'rotorStationSettings'];
    $notEndpoints = ['wrapper'];
    $found = [];

    foreach (glob($directory.'/*.py') ?: [] as $file) {
        $source = file_get_contents($file);

        if (false === $source) {
            continue;
        }

        preg_match_all('/^    def ([a-z][a-z_0-9]*)/m', $source, $matches);

        foreach ($matches[1] as $name) {
            if (str_starts_with($name, '_') || in_array($name, $notEndpoints, true)) {
                continue;
            }

            $camel = $renamed[$name] ?? lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
            $found[$camel] = $name;
        }
    }

    return $found;
}

/**
 * @return list<string>
 */
function ourMethods(): array
{
    $found = [];

    foreach ((new ReflectionClass(Client::class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        if (!$method->isConstructor()) {
            $found[] = $method->getName();
        }
    }

    sort($found);

    return $found;
}

$reference = referenceMethods($clientDir);
$ours = ourMethods();

$missing = array_diff_key($reference, array_flip($ours));
$extra = array_diff($ours, array_keys($reference));

printf("reference: %d methods\nhere:      %d methods\n\n", count($reference), count($ours));

if ([] === $missing) {
    echo "Nothing the reference has is missing here.\n";
} else {
    echo 'Missing here ('.count($missing)."):\n";

    foreach ($missing as $camel => $python) {
        echo "  {$camel}  ({$python})\n";
    }
}

// Ours-only is not a problem: some are conveniences, and some are endpoints
// the reference never wrapped. Listing them keeps the difference deliberate.
echo "\nHere but not in the reference (".count($extra)."):\n";

foreach ($extra as $name) {
    echo "  {$name}\n";
}

exit([] === $missing ? 0 : 1);
