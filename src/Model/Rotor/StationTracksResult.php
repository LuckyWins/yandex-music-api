<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What a station will play next.
 *
 * $batchId ties feedback back to this particular batch, and the feedback
 * methods take it for exactly that reason.
 */
final class StationTracksResult extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'id' => [Id::class, 'one'],
        'sequence' => [Sequence::class, 'list'],
    ];

    public function __construct(
        public readonly ?Id $id = null,
        /** @var list<Sequence> */
        public readonly array $sequence = [],
        public readonly ?string $batchId = null,
        public readonly ?bool $pumpkin = null,
        /** Identifies the listening session the feedback belongs to. */
        public readonly ?string $radioSessionId = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The tracks themselves, without the wrapping.
     *
     * @return list<\LuckyWins\YandexMusic\Model\Track\Track>
     */
    public function tracks(): array
    {
        $tracks = [];

        foreach ($this->sequence as $item) {
            if (null !== $item->track) {
                $tracks[] = $item->track;
            }
        }

        return $tracks;
    }

    protected function identity(): array
    {
        return [$this->id, $this->batchId];
    }
}
