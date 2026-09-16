<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Shot;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What the service wants played between two tracks.
 */
final class ShotEvent extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'shots' => [Shot::class, 'list'],
    ];

    public function __construct(
        public readonly string $eventId,
        /** @var list<Shot> */
        public readonly array $shots = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->eventId];
    }
}
