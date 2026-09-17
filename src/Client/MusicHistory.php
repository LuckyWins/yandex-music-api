<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistory as ListeningHistory;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryItems;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryQuery;

/**
 * The listening history — what was played, and what it was played from.
 */
trait MusicHistory
{
    /**
     * What the account has been listening to, a day at a time.
     *
     * @param int $fullModelsCount how many entries come back filled in rather
     *                             than as bare references
     */
    public function musicHistory(int $fullModelsCount = 10): ?ListeningHistory
    {
        return ListeningHistory::fromApi(
            $this->request->get($this->getBaseUrl().'/music-history', ['fullModelsCount' => $fullModelsCount]),
            $this,
        );
    }

    /**
     * Fill in particular entries of the history.
     *
     * Takes a query rather than the reference's five parallel lists, two of
     * which are lists of pairs — see MusicHistoryQuery.
     */
    public function musicHistoryItems(MusicHistoryQuery $query): ?MusicHistoryItems
    {
        return MusicHistoryItems::fromApi(
            $this->request->postJson($this->getBaseUrl().'/music-history/items', $query->toArray()),
            $this,
        );
    }
}
