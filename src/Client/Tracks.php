<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use LuckyWins\YandexMusic\Model\Credits;
use LuckyWins\YandexMusic\Model\Disclaimer;
use LuckyWins\YandexMusic\Model\Shot\ShotEvent;
use LuckyWins\YandexMusic\Model\Supplement\Supplement;
use LuckyWins\YandexMusic\Model\Track\DownloadInfo;
use LuckyWins\YandexMusic\Model\Track\SimilarTracks;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\Track\TrackFullInfo;
use LuckyWins\YandexMusic\Model\Track\TrackLyrics;
use LuckyWins\YandexMusic\Model\Track\TrackTrailer;

/**
 * Tracks: fetching them, their extras, and their audio.
 *
 * Mind which identifier each endpoint wants. Playback and likes take the
 * composite `{trackId}:{albumId}` that Track::compositeId() builds; everything
 * here takes the bare id. Passing the wrong one tends to return nothing rather
 * than fail, so it is worth being deliberate.
 */
trait Tracks
{
    /**
     * The key the Android app signs lyrics requests with. Public in the same
     * sense as the OAuth credentials: extracted, not issued.
     */
    private const LYRICS_SIGN_KEY = 'p93jhgh689SBReK6ghtw62';

    /**
     * Fetch tracks by id.
     *
     * @param string|int|list<string|int> $trackIds
     *
     * @return list<Track>
     */
    public function tracks(string|int|array $trackIds, bool $withPositions = true): array
    {
        $result = $this->request->post($this->getBaseUrl().'/tracks', [
            'track-ids' => is_array($trackIds) ? implode(',', $trackIds) : $trackIds,
            'with-positions' => $withPositions ? 'True' : 'False',
        ]);

        return Track::listFromApi($result, $this);
    }

    /**
     * The ways a track can be downloaded.
     *
     * Each manifest is good for about a minute, so resolve the one you want
     * promptly rather than holding the list.
     *
     * @return list<DownloadInfo>
     */
    public function tracksDownloadInfo(string|int $trackId): array
    {
        $result = $this->request->get($this->getBaseUrl().'/tracks/'.$trackId.'/download-info');

        return DownloadInfo::listFromApi($result, $this);
    }

    /**
     * Videos and, for podcasts, the full description.
     *
     * The lyrics this also carries are deprecated; use tracksLyrics() instead.
     */
    public function trackSupplement(string|int $trackId): ?Supplement
    {
        return Supplement::fromApi($this->request->get($this->getBaseUrl().'/tracks/'.$trackId.'/supplement'), $this);
    }

    /**
     * Where to fetch a track's lyrics.
     *
     * Needs a token, and a signature this method produces for you. Check
     * Track::$lyricsInfo before asking for the synced form — a track without
     * it answers not-found rather than coming back empty.
     *
     * @param string $format `TEXT` for plain lyrics, `LRC` for timed lines
     */
    public function tracksLyrics(string|int $trackId, string $format = 'TEXT'): ?TrackLyrics
    {
        $timestamp = time();

        $result = $this->request->get($this->getBaseUrl().'/tracks/'.$trackId.'/lyrics', [
            'format' => $format,
            'timeStamp' => $timestamp,
            'sign' => self::lyricsSignature($trackId, $timestamp),
        ]);

        return TrackLyrics::fromApi($result, $this);
    }

    public function tracksSimilar(string|int $trackId): ?SimilarTracks
    {
        return SimilarTracks::fromApi($this->request->get($this->getBaseUrl().'/tracks/'.$trackId.'/similar'), $this);
    }

    public function tracksTrailer(string|int $trackId): ?TrackTrailer
    {
        return TrackTrailer::fromApi($this->request->get($this->getBaseUrl().'/tracks/'.$trackId.'/trailer'), $this);
    }

    public function tracksFullInfo(string|int $trackId): ?TrackFullInfo
    {
        return TrackFullInfo::fromApi($this->request->get($this->getBaseUrl().'/tracks/'.$trackId.'/full-info'), $this);
    }

    public function tracksCredits(string|int $trackId): ?Credits
    {
        return Credits::fromApi($this->request->get($this->getBaseUrl().'/tracks/'.$trackId.'/credits'), $this);
    }

    /**
     * Notices that must accompany a track.
     *
     * A list, despite the singular path and despite the reference library
     * declaring a single object — checked against the live API.
     *
     * @return list<Disclaimer>
     */
    public function tracksDisclaimer(string|int $trackId): array
    {
        $result = $this->request->get($this->getBaseUrl().'/tracks/'.$trackId.'/disclaimer');

        return Disclaimer::listFromApi($result, $this);
    }

    /**
     * Report that a track was played.
     *
     * This is what feeds the listening history and the recommendations built
     * on it. The service is lenient about the figures, but reporting nonsense
     * pollutes the account's own suggestions.
     *
     * @param string $from a label for the client doing the playing
     */
    public function playAudio(
        string|int $trackId,
        string $from,
        string|int $albumId,
        string|int|null $playlistId = null,
        bool $fromCache = false,
        ?string $playId = null,
        ?int $uid = null,
        int $trackLengthSeconds = 0,
        int $totalPlayedSeconds = 0,
        int $endPositionSeconds = 0,
    ): bool {
        // The reference stamps local time and labels it Z, which is wrong
        // wherever the machine is not on UTC. Send real UTC instead.
        $now = (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format(DateTimeInterface::ATOM);

        $result = $this->request->post($this->getBaseUrl().'/play-audio', [
            'track-id' => $trackId,
            'from-cache' => $fromCache ? 'True' : 'False',
            'from' => $from,
            'play-id' => $playId ?? '',
            'uid' => $uid ?? $this->getAccountUid() ?? '',
            'timestamp' => $now,
            'track-length-seconds' => $trackLengthSeconds,
            'total-played-seconds' => $totalPlayedSeconds,
            'end-position-seconds' => $endPositionSeconds,
            'album-id' => $albumId,
            'playlist-id' => $playlistId ?? '',
            'client-now' => $now,
        ]);

        return 'ok' === $result;
    }

    /**
     * What the service wants played between two tracks — one of Alice's spoken
     * interjections, typically.
     *
     * @param string $contextItem `{ownerId}:{playlistId}`
     */
    public function afterTrack(
        string|int $nextTrackId,
        string $contextItem,
        string|int|null $prevTrackId = null,
        string $context = 'playlist',
        string $types = 'shot',
        string $from = 'mobile-landing-origin-default',
    ): ?ShotEvent {
        $result = $this->request->get($this->getBaseUrl().'/after-track', [
            'from' => $from,
            'prevTrackId' => $prevTrackId ?? '',
            'nextTrackId' => $nextTrackId,
            'context' => $context,
            'contextItem' => $contextItem,
            'types' => $types,
        ]);

        // The payload wraps the event, and also carries advertising for
        // accounts without a subscription, which is not modelled.
        return is_array($result) ? ShotEvent::fromApi($result['shotEvent'] ?? null, $this) : null;
    }

    /**
     * Sign a lyrics request.
     *
     * HMAC-SHA256 over the track id followed by the timestamp, with no
     * separator, base64-encoded. An id of the composite form is reduced to its
     * track part first.
     */
    private static function lyricsSignature(string|int $trackId, int $timestamp): string
    {
        $id = is_string($trackId) ? strtok($trackId, ':') : (string) $trackId;

        return base64_encode(
            hash_hmac('sha256', ($id ?: '0').$timestamp, self::LYRICS_SIGN_KEY, true),
        );
    }
}
