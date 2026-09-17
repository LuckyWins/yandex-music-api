<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Everywhere concerts are listed for.
 */
final class ConcertLocations extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'locations' => [ConcertLocation::class, 'list'],
    ];

    public function __construct(
        /** @var list<ConcertLocation> */
        public readonly array $locations = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The id of a city by name, for handing to concertsFeed().
     */
    public function idOf(string $name): ?int
    {
        foreach ($this->locations as $location) {
            if ($location->name === $name) {
                return $location->id;
            }
        }

        return null;
    }

    protected function identity(): array
    {
        return [$this->locations];
    }
}
