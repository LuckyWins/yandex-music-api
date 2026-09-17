<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\MusicHistory;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One day of listening.
 */
final class MusicHistoryTab extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'items' => [MusicHistoryGroup::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $date = null,
        /** @var list<MusicHistoryGroup> */
        public readonly array $items = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->date];
    }
}
