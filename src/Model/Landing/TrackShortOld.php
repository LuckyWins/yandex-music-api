<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A track reference as the landing sends it: the pair of ids and when it was
 * played, without the track.
 *
 * Named for the reference library's class, which calls it old because the
 * rest of the API has moved on to the shape in Track\TrackShort.
 */
final class TrackShortOld extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'trackId' => [TrackId::class, 'one'],
    ];

    public function __construct(
        public readonly ?TrackId $trackId = null,
        public readonly ?string $timestamp = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->trackId, $this->timestamp];
    }
}
