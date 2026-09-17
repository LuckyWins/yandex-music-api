<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\MusicHistory;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The entries asked about, filled in.
 */
final class MusicHistoryItems extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'items' => [MusicHistoryItem::class, 'list'],
    ];

    public function __construct(
        /** @var list<MusicHistoryItem> */
        public readonly array $items = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->items];
    }
}
