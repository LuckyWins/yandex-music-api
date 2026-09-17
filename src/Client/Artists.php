<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Artist\AboutArtist;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Artist\ArtistAlbums;
use LuckyWins\YandexMusic\Model\Artist\ArtistClips;
use LuckyWins\YandexMusic\Model\Artist\ArtistDonations;
use LuckyWins\YandexMusic\Model\Artist\ArtistInfo;
use LuckyWins\YandexMusic\Model\Artist\ArtistLinks;
use LuckyWins\YandexMusic\Model\Artist\ArtistSkeleton;
use LuckyWins\YandexMusic\Model\Artist\ArtistTracks;
use LuckyWins\YandexMusic\Model\Artist\ArtistTrailer;
use LuckyWins\YandexMusic\Model\Artist\BriefInfo;
use LuckyWins\YandexMusic\Model\Artist\SimilarArtists;
use LuckyWins\YandexMusic\Model\Disclaimer;

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
     * Albums the artist made, in the discography arrangement.
     */
    public function artistsDiscographyAlbums(
        string|int $artistId,
        int $page = 0,
        int $pageSize = 20,
        string $sortBy = 'year',
    ): ?ArtistAlbums {
        return $this->artistAlbums($artistId, 'discography-albums', $page, $pageSize, $sortBy);
    }

    /**
     * The artist's own albums, with whatever the service considers unsafe
     * left out.
     */
    public function artistsSafeDirectAlbums(
        string|int $artistId,
        int $page = 0,
        int $pageSize = 20,
        string $sortBy = 'year',
    ): ?ArtistAlbums {
        return $this->artistAlbums($artistId, 'safe-direct-albums', $page, $pageSize, $sortBy);
    }

    /**
     * Every track id of the artist, unordered.
     *
     * Cheaper than artistsTrackIdsByRating() when the order does not matter.
     * The ids come back as strings, which is how this endpoint sends them.
     *
     * @return list<string>
     */
    public function artistsTrackIds(string|int $artistId): array
    {
        $result = $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/track-ids');
        $ids = is_array($result) ? ($result['tracks'] ?? $result) : [];

        if (!is_array($ids)) {
            return [];
        }

        $found = [];

        foreach ($ids as $id) {
            if (is_string($id) || is_int($id)) {
                $found[] = (string) $id;
            }
        }

        return $found;
    }

    /**
     * The artist's page: their description, covers and links.
     */
    public function artistsAbout(string|int $artistId): ?AboutArtist
    {
        return AboutArtist::fromApi(
            $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/about-artist'),
            $this,
        );
    }

    /**
     * An artist with the numbers around them, without the albums and tracks
     * that make artistsBriefInfo() heavy.
     */
    public function artistsInfo(string|int $artistId): ?ArtistInfo
    {
        return ArtistInfo::fromApi(
            $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/info'),
            $this,
        );
    }

    /**
     * Everywhere else the artist can be found.
     */
    public function artistsLinks(string|int $artistId): ?ArtistLinks
    {
        return ArtistLinks::fromApi(
            $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/artist-links'),
            $this,
        );
    }

    /**
     * A page of the artist's clips.
     */
    public function artistsClips(string|int $artistId): ?ArtistClips
    {
        return ArtistClips::fromApi(
            $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/blocks/artist-clips'),
            $this,
        );
    }

    /**
     * How the artist can be supported, when they accept support at all.
     */
    public function artistsDonation(string|int $artistId): ?ArtistDonations
    {
        return ArtistDonations::fromApi(
            $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/blocks/artist-donation'),
            $this,
        );
    }

    /**
     * The artist's trailer and the tracks it plays.
     */
    public function artistsTrailer(string|int $artistId): ?ArtistTrailer
    {
        return ArtistTrailer::fromApi(
            $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/trailer'),
            $this,
        );
    }

    /**
     * How the artist's page is laid out — which blocks to draw, and where each
     * one's contents come from.
     */
    public function artistsSkeleton(string|int $artistId, string $skeletonId): ?ArtistSkeleton
    {
        return ArtistSkeleton::fromApi(
            $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/skeletons/'.$skeletonId),
            $this,
        );
    }

    /**
     * Notices that must accompany an artist.
     *
     * A list, like the track and album variants, despite the reference
     * declaring a single object.
     *
     * @return list<Disclaimer>
     */
    public function artistsDisclaimer(string|int $artistId): array
    {
        return Disclaimer::listFromApi(
            $this->request->get($this->getBaseUrl().'/artists/'.$artistId.'/disclaimer'),
            $this,
        );
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
