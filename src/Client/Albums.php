<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Album\AlbumSimilarEntities;
use LuckyWins\YandexMusic\Model\Album\AlbumTrailer;
use LuckyWins\YandexMusic\Model\Disclaimer;

/**
 * Albums.
 */
trait Albums
{
    /**
     * Fetch albums by id.
     *
     * @param string|int|list<string|int> $albumIds
     *
     * @return list<Album>
     */
    public function albums(string|int|array $albumIds): array
    {
        $result = $this->request->post($this->getBaseUrl().'/albums', [
            'album-ids' => is_array($albumIds) ? implode(',', $albumIds) : $albumIds,
        ]);

        return Album::listFromApi($result, $this);
    }

    /**
     * One album, without its tracks.
     *
     * Cheaper than albumsWithTracks() when all you want is the artwork, the
     * year or the artist.
     */
    public function album(string|int $albumId): ?Album
    {
        return Album::fromApi($this->request->get($this->getBaseUrl().'/albums/'.$albumId), $this);
    }

    /**
     * One album with everything on it.
     *
     * Tracks arrive grouped by disc under `volumes`; Album::tracks() flattens
     * that if the grouping is not what you want.
     */
    public function albumsWithTracks(string|int $albumId): ?Album
    {
        return Album::fromApi($this->request->get($this->getBaseUrl().'/albums/'.$albumId.'/with-tracks'), $this);
    }

    /**
     * Notices that must accompany an album.
     *
     * @return list<Disclaimer>
     */
    public function albumsDisclaimer(string|int $albumId): array
    {
        $result = $this->request->get($this->getBaseUrl().'/albums/'.$albumId.'/disclaimer');

        return Disclaimer::listFromApi($result, $this);
    }

    /**
     * An album's trailer and the tracks it plays.
     */
    public function albumsTrailer(string|int $albumId): ?AlbumTrailer
    {
        return AlbumTrailer::fromApi(
            $this->request->get($this->getBaseUrl().'/albums/'.$albumId.'/trailer'),
            $this,
        );
    }

    /**
     * What to listen to next when the album runs out.
     */
    public function albumsSimilarEntities(string|int $albumId): ?AlbumSimilarEntities
    {
        return AlbumSimilarEntities::fromApi(
            $this->request->get($this->getBaseUrl().'/albums/'.$albumId.'/similar-entities'),
            $this,
        );
    }
}
