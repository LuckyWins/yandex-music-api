<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A clip and who is in it.
 */
final class ArtistClipData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'clip' => [Clip::class, 'one'],
        'artists' => [Artist::class, 'list'],
    ];

    public function __construct(
        public readonly ?Clip $clip = null,
        /** @var list<Artist> */
        public readonly array $artists = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->clip];
    }
}
