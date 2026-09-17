<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Everywhere else an artist can be found.
 */
final class ArtistLinks extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'links' => [ArtistLink::class, 'list'],
    ];

    public function __construct(
        /** @var list<ArtistLink> */
        public readonly array $links = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->links];
    }
}
