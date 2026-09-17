<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Label;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;

/**
 * A page of a label's releases.
 */
final class LabelAlbums extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'albums' => [Album::class, 'list'],
        'pager' => [Pager::class, 'one'],
    ];

    public function __construct(
        /** @var list<Album> */
        public readonly array $albums = [],
        public readonly ?Pager $pager = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->albums, $this->pager];
    }
}
