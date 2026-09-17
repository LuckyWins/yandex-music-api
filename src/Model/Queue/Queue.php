<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Queue;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What a device is playing, so another device can pick it up.
 *
 * The tracks are references rather than tracks; fetch them with tracks() when
 * they are wanted.
 */
final class Queue extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'context' => [Context::class, 'one'],
        'tracks' => [TrackId::class, 'list'],
    ];

    public function __construct(
        public readonly ?Context $context = null,
        /** @var list<TrackId> */
        public readonly array $tracks = [],
        public readonly ?int $currentIndex = null,
        public readonly ?string $modified = null,
        public readonly ?string $id = null,
        /** Where playback was started from; `from` on the wire. */
        public readonly ?string $from = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The track playing now, if the queue says which.
     */
    public function current(): ?TrackId
    {
        return null === $this->currentIndex ? null : ($this->tracks[$this->currentIndex] ?? null);
    }

    protected function identity(): array
    {
        return [$this->id, $this->modified];
    }
}
