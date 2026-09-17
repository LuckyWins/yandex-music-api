<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\MusicHistory;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What the account has been listening to, a day at a time.
 */
final class MusicHistory extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'historyTabs' => [MusicHistoryTab::class, 'list'],
    ];

    public function __construct(
        /** @var list<MusicHistoryTab> */
        public readonly array $historyTabs = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->historyTabs];
    }
}
