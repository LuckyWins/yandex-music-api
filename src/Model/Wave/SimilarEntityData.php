<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Wave;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What a similar entity actually is, once its type has said which half of
 * this to read.
 */
final class SimilarEntityData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'wave' => [Wave::class, 'one'],
        'agent' => [WaveAgent::class, 'one'],
        'album' => [Album::class, 'one'],
        'artist' => [Artist::class, 'one'],
        'artists' => [Artist::class, 'list'],
    ];

    public function __construct(
        public readonly ?Wave $wave = null,
        public readonly ?WaveAgent $agent = null,
        /** An album's similar entities point at albums rather than at waves. */
        public readonly ?Album $album = null,
        public readonly ?Artist $artist = null,
        /** @var list<Artist> */
        public readonly array $artists = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->wave, $this->agent, $this->album, $this->artist];
    }
}
