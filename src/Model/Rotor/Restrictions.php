<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What a station can be tuned to.
 *
 * Each field describes one setting and the values it accepts, so an interface
 * can be built without knowing the vocabulary in advance — and so this library
 * does not have to be the authority on what is allowed.
 */
final class Restrictions extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'language' => [Enum::class, 'one'],
        'diversity' => [Enum::class, 'one'],
        'moodEnergy' => [Enum::class, 'one'],
        'mood' => [DiscreteScale::class, 'one'],
        'energy' => [DiscreteScale::class, 'one'],
    ];

    public function __construct(
        public readonly ?Enum $language = null,
        public readonly ?Enum $diversity = null,
        public readonly ?DiscreteScale $mood = null,
        public readonly ?DiscreteScale $energy = null,
        public readonly ?Enum $moodEnergy = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->language, $this->diversity, $this->moodEnergy];
    }
}
