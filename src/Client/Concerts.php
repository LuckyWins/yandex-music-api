<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Concert\ConcertFeed;
use LuckyWins\YandexMusic\Model\Concert\ConcertInfo;
use LuckyWins\YandexMusic\Model\Concert\ConcertLocations;
use LuckyWins\YandexMusic\Model\Concert\ConcertSkeleton;
use LuckyWins\YandexMusic\Model\Concert\ConcertTabConfig;

/**
 * Concerts — what is on, and where.
 *
 * A concert is identified by a uuid rather than a number, and the listing is
 * filtered by city: ask concertsLocations() for the ids.
 */
trait Concerts
{
    /**
     * Everywhere concerts are listed for.
     */
    public function concertsLocations(): ?ConcertLocations
    {
        return ConcertLocations::fromApi($this->request->get($this->getBaseUrl().'/concerts/locations'), $this);
    }

    /**
     * How much of the concerts tab to show at once.
     */
    public function concertsTabConfig(): ?ConcertTabConfig
    {
        return ConcertTabConfig::fromApi($this->request->get($this->getBaseUrl().'/concerts/tab-config'), $this);
    }

    /**
     * What is on.
     *
     * @param list<string|int>|null $locations city ids from concertsLocations();
     *                                         everywhere when omitted
     */
    public function concertsFeed(?array $locations = null): ?ConcertFeed
    {
        $params = null === $locations || [] === $locations
            ? []
            : ['locations' => implode(',', $locations)];

        return ConcertFeed::fromApi($this->request->get($this->getBaseUrl().'/concerts/feed', $params), $this);
    }

    /**
     * One concert's page.
     */
    public function concertInfo(string $concertId): ?ConcertInfo
    {
        return ConcertInfo::fromApi(
            $this->request->get($this->getBaseUrl().'/concerts/'.$concertId.'/info'),
            $this,
        );
    }

    /**
     * How a concert's page is laid out — the same skeleton models an artist's
     * page uses.
     */
    public function concertSkeleton(string $concertId, string $skeletonId = 'concert_page'): ?ConcertSkeleton
    {
        return ConcertSkeleton::fromApi(
            $this->request->get($this->getBaseUrl().'/concerts/'.$concertId.'/skeletons/'.$skeletonId),
            $this,
        );
    }
}
