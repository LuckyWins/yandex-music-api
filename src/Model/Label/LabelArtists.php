<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Label;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;

/**
 * A page of the artists signed to a label.
 */
final class LabelArtists extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'artists' => [Artist::class, 'list'],
        'pager' => [Pager::class, 'one'],
    ];

    public function __construct(
        /** @var list<Artist> */
        public readonly array $artists = [],
        public readonly ?Pager $pager = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->artists, $this->pager];
    }
}
