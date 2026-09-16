<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic;

/**
 * The passage of time, injectable so that polling loops can be tested without
 * actually waiting.
 *
 * `now()` is monotonic: only differences between two readings mean anything,
 * and it does not jump when the system clock is adjusted.
 */
interface Clock
{
    public function now(): float;

    public function sleep(float $seconds): void;
}
