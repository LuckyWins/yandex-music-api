<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\MusicHistory;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A stretch of listening: what it was played from, and what was played.
 */
final class MusicHistoryGroup extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'context' => [MusicHistoryItem::class, 'one'],
        'tracks' => [MusicHistoryItem::class, 'list'],
    ];

    public function __construct(
        public readonly ?MusicHistoryItem $context = null,
        /** @var list<MusicHistoryItem> */
        public readonly array $tracks = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->context, $this->tracks];
    }
}
