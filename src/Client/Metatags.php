<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Metatag\Metatag;
use LuckyWins\YandexMusic\Model\Metatag\MetatagAlbums;
use LuckyWins\YandexMusic\Model\Metatag\MetatagArtists;
use LuckyWins\YandexMusic\Model\Metatag\MetatagPlaylists;
use LuckyWins\YandexMusic\Model\Metatag\Metatags as MetatagTrees;

/**
 * Metatags — the themed selections: a mood, an era, an occasion.
 *
 * A tag is named by a string, and not always an ASCII one, so the id is
 * encoded into the path rather than pasted in: an unencoded one comes back as
 * "not found" with the name mangled in the message.
 */
trait Metatags
{
    /**
     * Every way the catalogue is tagged, as trees to navigate.
     */
    public function metatags(): ?MetatagTrees
    {
        return MetatagTrees::fromApi($this->request->get($this->getBaseUrl().'/landing3/metatags'), $this);
    }

    /**
     * A tag's page: a bit of everything filed under it.
     *
     * How much of each comes back is asked for here; leave a count out and
     * the service decides. The sort values a tag accepts are advertised in
     * its own response, like a radio station's settings.
     *
     * @param string|null $tracksSortBy `popular` or `new`
     * @param string|null $albumsSortBy `popular` or `new`
     */
    public function metatag(
        string $metatagId,
        ?int $tracksCount = null,
        ?int $artistsCount = null,
        ?int $composersCount = null,
        ?int $albumsCount = null,
        ?int $promotionsCount = null,
        ?int $featuresCount = null,
        ?int $playlistsCount = null,
        ?int $concertsCount = null,
        ?string $tracksSortBy = null,
        ?string $albumsSortBy = null,
        ?bool $withLikesCount = null,
    ): ?Metatag {
        $params = self::onlyGiven([
            'tracksCount' => $tracksCount,
            'artistsCount' => $artistsCount,
            'composersCount' => $composersCount,
            'albumsCount' => $albumsCount,
            'promotionsCount' => $promotionsCount,
            'featuresCount' => $featuresCount,
            'playlistsCount' => $playlistsCount,
            'concertsCount' => $concertsCount,
            'tracksSortBy' => $tracksSortBy,
            'albumsSortBy' => $albumsSortBy,
            'withLikesCount' => null === $withLikesCount ? null : ($withLikesCount ? 'true' : 'false'),
        ]);

        return Metatag::fromApi(
            $this->request->get($this->getBaseUrl().'/metatags/'.rawurlencode($metatagId), $params),
            $this,
        );
    }

    /**
     * A page of the albums under a tag.
     *
     * @param string|null $period `month`, `year`, and whatever else the tag advertises
     */
    public function metatagAlbums(
        string $metatagId,
        int $offset = 0,
        int $limit = 20,
        ?string $period = null,
        ?string $sortBy = null,
    ): ?MetatagAlbums {
        $params = self::onlyGiven(['offset' => $offset, 'limit' => $limit, 'period' => $period, 'sortBy' => $sortBy]);

        return MetatagAlbums::fromApi(
            $this->request->get($this->getBaseUrl().'/metatags/'.rawurlencode($metatagId).'/albums', $params),
            $this,
        );
    }

    /**
     * A page of the artists under a tag, each with a few of their tracks.
     */
    public function metatagArtists(
        string $metatagId,
        string $period = 'month',
        int $offset = 0,
        int $limit = 20,
        ?string $sortBy = null,
        ?int $tracksPerArtist = null,
    ): ?MetatagArtists {
        $params = self::onlyGiven([
            'period' => $period,
            'offset' => $offset,
            'limit' => $limit,
            'sortBy' => $sortBy,
            'tracksPerArtist' => $tracksPerArtist,
        ]);

        return MetatagArtists::fromApi(
            $this->request->get($this->getBaseUrl().'/metatags/'.rawurlencode($metatagId).'/artists', $params),
            $this,
        );
    }

    /**
     * A page of the playlists under a tag.
     */
    public function metatagPlaylists(
        string $metatagId,
        int $offset = 0,
        int $limit = 20,
        ?string $sortBy = null,
    ): ?MetatagPlaylists {
        $params = self::onlyGiven(['offset' => $offset, 'limit' => $limit, 'sortBy' => $sortBy]);

        return MetatagPlaylists::fromApi(
            $this->request->get($this->getBaseUrl().'/metatags/'.rawurlencode($metatagId).'/playlists', $params),
            $this,
        );
    }

    /**
     * Drop what was not asked for, so an omitted count stays omitted rather
     * than going out as an empty parameter.
     *
     * @param array<string, scalar|null> $params
     *
     * @return array<string, scalar>
     */
    private static function onlyGiven(array $params): array
    {
        return array_filter($params, static fn (string|int|float|bool|null $value): bool => null !== $value);
    }
}
