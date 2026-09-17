<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Where an artist is playing next.
 */
final class ArtistConcerts extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'concerts' => [Concert::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $artistTitle = null,
        /** @var list<Concert> */
        public readonly array $concerts = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->artistTitle, $this->concerts];
    }
}
