<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Examples\Support;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use ReflectionClass;
use ReflectionMethod;

/**
 * Keeps score of a sweep over the API: what was called, what answered, what
 * was left alone on purpose, and what was never reached at all.
 *
 * The last of those is the reason this class exists. A sweep that silently
 * stops covering an endpoint added later is worse than no sweep, because it
 * still prints a clean report. So the endpoints are taken from the client by
 * reflection rather than from a list written by hand, and anything neither
 * called nor deliberately skipped is named in the report.
 */
final class Sweep
{
    /** @var array<string, string> endpoint to what happened: '' when it answered */
    private array $called = [];

    /** @var array<string, string> endpoint to why it was left alone */
    private array $skipped = [];

    /**
     * Told which call is running, so that a field it could not place is
     * reported with the call it arrived on rather than on its own.
     */
    public function __construct(private readonly ?UnknownFieldCollector $collector = null)
    {
    }

    /**
     * Run one call, record the endpoint, and let a refusal stop only that call.
     *
     * The endpoint is named rather than inferred: there is no way to see which
     * method a closure went on to invoke. A name that is not a method of the
     * client shows up in the report, so a typo is loud rather than silent.
     *
     * @template T
     *
     * @param callable(): T $call
     *
     * @return T|null
     */
    public function run(string $endpoint, callable $call): mixed
    {
        $this->collector?->during($endpoint);

        try {
            $result = $call();
        } catch (YandexMusicException $e) {
            $this->called[$endpoint] = $e->getMessage();

            printf("  %-36s FAILED: %s\n", $endpoint, $e->getMessage());

            return null;
        } finally {
            $this->collector?->during(null);
        }

        // An endpoint that answers with nothing has not really been exercised:
        // no fields arrived, so no field could be found missing. Saying so is
        // the difference between a sweep and the appearance of one.
        $note = match (true) {
            null === $result => 'nothing came back',
            is_array($result) && [] === $result => 'empty',
            default => '',
        };

        $this->called[$endpoint] = '';

        printf("  %-36s %s\n", $endpoint, $note);

        return $result;
    }

    /**
     * Record an endpoint this sweep will not call, and why.
     */
    public function skip(string $endpoint, string $why): void
    {
        $this->skipped[$endpoint] = $why;
    }

    /**
     * @param array<string, string> $endpoints endpoint to why
     */
    public function skipAll(array $endpoints): void
    {
        foreach ($endpoints as $endpoint => $why) {
            $this->skip($endpoint, $why);
        }
    }

    /**
     * What the sweep reached, what it refused to reach, and what it missed.
     */
    public function report(): string
    {
        $endpoints = self::endpoints();
        $failed = array_filter($this->called, static fn (string $why): bool => '' !== $why);
        $missed = array_values(array_diff($endpoints, array_keys($this->called), array_keys($this->skipped)));
        $unknown = array_values(array_diff(
            [...array_keys($this->called), ...array_keys($this->skipped)],
            $endpoints,
        ));

        $out = sprintf(
            "%d of %d endpoint(s) called, %d answered with an error, %d left alone, %d not covered\n",
            count($this->called),
            count($endpoints),
            count($failed),
            count($this->skipped),
            count($missed),
        );

        if ([] !== $failed) {
            $out .= "\nanswered with an error:\n";

            foreach ($failed as $endpoint => $why) {
                $out .= sprintf("  %-36s %s\n", $endpoint, $why);
            }
        }

        if ([] !== $this->skipped) {
            $out .= "\nleft alone on purpose:\n";
            $reasons = [];

            foreach ($this->skipped as $endpoint => $why) {
                $reasons[$why][] = $endpoint;
            }

            foreach ($reasons as $why => $group) {
                sort($group);
                $out .= sprintf("  %-10s %s\n", $why, implode(', ', $group));
            }
        }

        $out .= "\nnot covered:\n";
        $out .= [] === $missed
            ? "  none — every endpoint is either called or deliberately skipped\n"
            : '  '.implode("\n  ", $missed)."\n";

        if ([] !== $unknown) {
            $out .= "\nnamed here but not a method of the client — a typo in this sweep:\n";
            $out .= '  '.implode(', ', $unknown)."\n";
        }

        return $out;
    }

    /**
     * Every endpoint the client offers.
     *
     * The traits under src/Client are the endpoints; the client's own methods
     * are its token, its language and the like. Reflection tells them apart by
     * file, because a method defined in a trait keeps the trait's file even
     * once it has been composed into the class.
     *
     * @return list<string>
     */
    public static function endpoints(): array
    {
        $directory = DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'Client'.DIRECTORY_SEPARATOR;
        $names = [];

        foreach ((new ReflectionClass(Client::class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $file = $method->getFileName();

            if (is_string($file) && str_contains($file, $directory)) {
                $names[] = $method->getName();
            }
        }

        sort($names);

        return $names;
    }
}
