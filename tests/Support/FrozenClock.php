<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Support;

use LuckyWins\YandexMusic\Clock;

/**
 * A clock that only moves when a test says so, or when something sleeps on it.
 */
final class FrozenClock implements Clock
{
    /** @var list<float> */
    private array $slept = [];

    public function __construct(private float $time = 0.0)
    {
    }

    public function now(): float
    {
        return $this->time;
    }

    public function sleep(float $seconds): void
    {
        $this->slept[] = $seconds;
        $this->time += $seconds;
    }

    public function advance(float $seconds): void
    {
        $this->time += $seconds;
    }

    /** @return list<float> every interval that was slept, in order */
    public function sleeps(): array
    {
        return $this->slept;
    }
}
