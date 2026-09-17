<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Presave;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Albums the account asked to be told about: the ones still to come, and the
 * ones that have since come out.
 */
final class Presaves extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'upcomingAlbums' => [Album::class, 'list'],
        'releasedAlbums' => [Album::class, 'list'],
    ];

    public function __construct(
        /** @var list<Album> */
        public readonly array $upcomingAlbums = [],
        /** @var list<Album> */
        public readonly array $releasedAlbums = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->upcomingAlbums, $this->releasedAlbums];
    }
}
