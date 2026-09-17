<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\MusicHistory;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What an entry of the history points at.
 *
 * One shape for every kind: a track fills $trackId and $albumId, an album or
 * artist fills $id, a playlist fills $uid with $kind, a wave fills $seeds.
 * The entry's own type says which to read.
 */
final class MusicHistoryItemId extends Model
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $trackId = null,
        public readonly ?string $albumId = null,
        public readonly string|int|null $uid = null,
        public readonly string|int|null $kind = null,
        /** @var list<string> */
        public readonly array $seeds = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->trackId, $this->uid, $this->kind, $this->seeds];
    }
}
