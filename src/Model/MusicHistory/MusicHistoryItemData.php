<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\MusicHistory;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * An entry's identity and, when asked for, the thing itself.
 *
 * $fullModel is a track for a track and a context for everything else, which
 * only the entry's type can say — see MusicHistoryItem.
 */
final class MusicHistoryItemData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'itemId' => [MusicHistoryItemId::class, 'one'],
    ];

    public function __construct(
        public readonly ?MusicHistoryItemId $itemId = null,
        public readonly Track|MusicHistoryContextFullModel|null $fullModel = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->itemId];
    }
}
