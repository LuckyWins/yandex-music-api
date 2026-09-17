<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * How a track sounds, as the station's own analysis measures it: tempo, a hue
 * to paint it with, and how energetic it is.
 *
 * The reference library does not model this at all.
 */
final class TrackParameters extends Model
{
    public function __construct(
        public readonly ?int $bpm = null,
        public readonly ?int $hue = null,
        public readonly ?float $energy = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->bpm, $this->hue, $this->energy];
    }
}
