<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * How a station is currently tuned.
 *
 * The values stay as the strings the API sent, so a value this library has
 * not heard of is still readable and can be sent back unchanged. The typed
 * accessors say whether a value is one of the ones we know: null there means
 * new, not empty — the raw field still holds it.
 */
final class RotorSettings extends Model
{
    public function __construct(
        public readonly ?string $language = null,
        public readonly ?string $diversity = null,
        public readonly ?int $mood = null,
        public readonly ?int $energy = null,
        public readonly ?string $moodEnergy = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The language as one of the values this library knows, or null when the
     * service has sent something new. The raw value is always in $language.
     */
    public function languageOption(): ?StationLanguage
    {
        return null === $this->language ? null : StationLanguage::tryFrom($this->language);
    }

    public function diversityOption(): ?Diversity
    {
        return null === $this->diversity ? null : Diversity::tryFrom($this->diversity);
    }

    public function moodEnergyOption(): ?MoodEnergy
    {
        return null === $this->moodEnergy ? null : MoodEnergy::tryFrom($this->moodEnergy);
    }

    protected function identity(): array
    {
        return [$this->language, $this->diversity, $this->moodEnergy, $this->mood, $this->energy];
    }
}
