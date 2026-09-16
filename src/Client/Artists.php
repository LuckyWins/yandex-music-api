<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Artist\ArtistAlbums;
use LuckyWins\YandexMusic\Model\Artist\ArtistTracks;
use LuckyWins\YandexMusic\Model\Artist\BriefInfo;
use LuckyWins\YandexMusic\Model\Artist\SimilarArtists;

/**
 * Artists.
 *
 * The listing endpoints here are paged the same way throughout: a page number
 * and a size going out, a Pager coming back with the total.
 */
trait Artists
{
    /**
     * Fetch artists by id.
     *
     * @param string|int|list<string|int> $artistIds
     *
     * @return list<Artist>
     */
    public function artists(string|int|array $artistIds): array
    {
        $result = $this->request->post($this->getBaseUrl().'/artists', [
            'artist-ids' => is_array($artistIds) ? implode(',', $artistIds) : $artistIds,
        ]);

        return Artist::listFromApi($result, $this);
    }

    /**
     * Everything the service will say about an artist at once — albums,
     * popular tracks, similar artists, covers, chart positions.
     *
     * Parts of the response are left raw because they belong to domains this
     * library has not ported yet. See BriefInfo.
     */
    public function artistsBriefInfo(string|int $artistId): ?BriefInfo
    {
        return BriefInfo::fromApi($this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/brief-info'), $this);
    }

    /**
     * A page of an artist's tracks, most popular first.
     */
    public function artistsTracks(string|int $artistId, int $page = 0, int $pageSize = 20): ?ArtistTracks
    {
        $result = $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/tracks', [
            'page' => $page,
            'page-size' => $pageSize,
        ]);

        return ArtistTracks::fromApi($result, $this);
    }

    /**
     * A page of the albums an artist made.
     *
     * @param string $sortBy `year` or `rating`
     */
    public function artistsDirectAlbums(
        string|int $artistId,
        int $page = 0,
        int $pageSize = 20,
        string $sortBy = 'year',
    ): ?ArtistAlbums {
        return $this->artistAlbums($artistId, 'direct-albums', $page, $pageSize, $sortBy);
    }

    /**
     * A page of the albums an artist appears on without being their author —
     * compilations, guest spots.
     *
     * @param string $sortBy `year` or `rating`
     */
    public function artistsAlsoAlbums(
        string|int $artistId,
        int $page = 0,
        int $pageSize = 20,
        string $sortBy = 'year',
    ): ?ArtistAlbums {
        return $this->artistAlbums($artistId, 'also-albums', $page, $pageSize, $sortBy);
    }

    /**
     * Who else sounds like this artist.
     */
    public function artistsSimilar(string|int $artistId): ?SimilarArtists
    {
        return SimilarArtists::fromApi(
            $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/similar'),
            $this,
        );
    }

    /**
     * The artist's tracks as bare ids, ordered by rating.
     *
     * Cheaper than artistsTracks() when the ids are all you need; fetch the
     * ones you want with tracks().
     *
     * @return list<int>
     */
    public function artistsTrackIdsByRating(string|int $artistId): array
    {
        $result = $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/track-ids-by-rating');
        $ids = is_array($result) ? ($result['tracks'] ?? []) : [];

        if (!is_array($ids)) {
            return [];
        }

        return array_values(array_map(intval(...), array_filter($ids, is_scalar(...))));
    }

    /**
     * The two album listings differ only in their path.
     */
    private function artistAlbums(
        string|int $artistId,
        string $kind,
        int $page,
        int $pageSize,
        string $sortBy,
    ): ?ArtistAlbums {
        $result = $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/'.$kind, [
            'page' => $page,
            'page-size' => $pageSize,
            'sort-by' => $sortBy,
        ]);

        return ArtistAlbums::fromApi($result, $this);
    }
}
