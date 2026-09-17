<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Account\Status;
use LuckyWins\YandexMusic\Model\Rotor\Dashboard;
use LuckyWins\YandexMusic\Model\Rotor\FeedbackType;
use LuckyWins\YandexMusic\Model\Rotor\Id;
use LuckyWins\YandexMusic\Model\Rotor\StationResult;
use LuckyWins\YandexMusic\Model\Rotor\StationTracksResult;

/**
 * Radio.
 *
 * A station is named by kind and tag — `genre:allrock`, `user:onyourwave`,
 * `mood:sad`. Every method here takes either that string or the Id model the
 * responses carry.
 *
 * Playing a station is a conversation: ask for tracks, then report back what
 * happened to them. The feedback is what makes the next batch make sense, and
 * it cannot be taken back once sent.
 */
trait Radio
{
    /**
     * The account as radio sees it — the same model as accountStatus(), with
     * a few extra fields filled in such as how many skips are left this hour.
     */
    public function rotorAccountStatus(): ?Status
    {
        return Status::fromApi($this->request->get($this->getBaseUrl().'/rotor/account/status'), $this);
    }

    /**
     * The stations offered to this account.
     */
    public function rotorStationsDashboard(): ?Dashboard
    {
        return Dashboard::fromApi($this->request->get($this->getBaseUrl().'/rotor/stations/dashboard'), $this);
    }

    /**
     * Every station there is.
     *
     * @param string|null $language the language to name them in; the client's own when omitted
     *
     * @return list<StationResult>
     */
    public function rotorStationsList(?string $language = null): array
    {
        $params = null === $language ? [] : ['language' => $language];

        return StationResult::listFromApi(
            $this->request->get($this->getBaseUrl().'/rotor/stations/list', $params),
            $this,
        );
    }

    /**
     * One station, with how it is tuned.
     *
     * A list of one, which is how the endpoint answers.
     *
     * @return list<StationResult>
     */
    public function rotorStationInfo(string|Id $station): array
    {
        return StationResult::listFromApi(
            $this->request->get($this->getBaseUrl().'/rotor/station/'.self::stationTag($station).'/info'),
            $this,
        );
    }

    /**
     * Tune a station.
     *
     * The values are whatever the station's Restrictions advertise: pass one
     * of this library's enums, or a bare string when the service has grown a
     * value this library does not know yet.
     *
     * The reference calls this `rotor_station_settings2` while posting to
     * `/settings3`. The endpoint's name is Yandex's business; the method's
     * name need not carry a version that means nothing to the caller.
     */
    public function rotorStationSettings(
        string|Id $station,
        string|\BackedEnum|null $moodEnergy = null,
        string|\BackedEnum|null $diversity = null,
        string|\BackedEnum|null $type = null,
    ): bool {
        $data = array_filter([
            'moodEnergy' => self::wireValue($moodEnergy),
            'diversity' => self::wireValue($diversity),
            'type' => self::wireValue($type),
        ], static fn (?string $value): bool => null !== $value);

        // JSON, like the feedback endpoints: a form is refused outright with a
        // 415 here. The reference sends a form.
        $result = $this->request->postJson(
            $this->getBaseUrl().'/rotor/station/'.self::stationTag($station).'/settings3',
            $data,
        );

        return 'ok' === $result;
    }

    /**
     * What the station will play next.
     *
     * @param bool        $settings2 ask for the station's settings alongside the tracks
     * @param string|null $queue     the id of the last track played, so the batch continues
     *                               rather than starting over
     */
    public function rotorStationTracks(
        string|Id $station,
        bool $settings2 = true,
        ?string $queue = null,
    ): ?StationTracksResult {
        $params = [];

        if ($settings2) {
            $params['settings2'] = 'True';
        }

        if (null !== $queue) {
            $params['queue'] = $queue;
        }

        return StationTracksResult::fromApi(
            $this->request->get($this->getBaseUrl().'/rotor/station/'.self::stationTag($station).'/tracks', $params),
            $this,
        );
    }

    /**
     * Tell a station what happened.
     *
     * Prefer the four named methods below; this one is what they are built on,
     * and what to reach for if the service grows a fifth kind of event.
     *
     * @param float|null $timestamp when it happened, as a Unix timestamp; now when omitted
     * @param string|null $from     where playback was started from, such as
     *                              `mobile-radio-user-123456789`
     */
    public function rotorStationFeedback(
        string|Id $station,
        FeedbackType|string $type,
        ?float $timestamp = null,
        ?string $from = null,
        string|int|null $trackId = null,
        int|float|null $totalPlayedSeconds = null,
        ?string $batchId = null,
    ): bool {
        $url = $this->getBaseUrl().'/rotor/station/'.self::stationTag($station).'/feedback';

        if (null !== $batchId) {
            $url .= '?'.http_build_query(['batch-id' => $batchId]);
        }

        $data = [
            'type' => $type instanceof FeedbackType ? $type->value : $type,
            'timestamp' => $timestamp ?? $this->clock->now(),
        ];

        if (null !== $trackId) {
            $data['trackId'] = $trackId;
        }

        if (null !== $from) {
            $data['from'] = $from;
        }

        if (null !== $totalPlayedSeconds) {
            $data['totalPlayedSeconds'] = $totalPlayedSeconds;
        }

        // JSON rather than a form: this endpoint refuses form bodies with a
        // 400, which is where the reference library's feedback stops working.
        return 'ok' === $this->request->postJson($url, $data);
    }

    /**
     * Playback of the station has begun.
     */
    public function rotorStationFeedbackRadioStarted(
        string|Id $station,
        ?string $from = null,
        ?string $batchId = null,
        ?float $timestamp = null,
    ): bool {
        return $this->rotorStationFeedback(
            $station,
            FeedbackType::RadioStarted,
            $timestamp,
            from: $from,
            batchId: $batchId,
        );
    }

    /**
     * A track has started playing.
     */
    public function rotorStationFeedbackTrackStarted(
        string|Id $station,
        string|int $trackId,
        ?string $batchId = null,
        ?float $timestamp = null,
    ): bool {
        return $this->rotorStationFeedback(
            $station,
            FeedbackType::TrackStarted,
            $timestamp,
            trackId: $trackId,
            batchId: $batchId,
        );
    }

    /**
     * A track has played to the end — or as far as it got.
     */
    public function rotorStationFeedbackTrackFinished(
        string|Id $station,
        string|int $trackId,
        int|float $totalPlayedSeconds,
        ?string $batchId = null,
        ?float $timestamp = null,
    ): bool {
        return $this->rotorStationFeedback(
            $station,
            FeedbackType::TrackFinished,
            $timestamp,
            trackId: $trackId,
            totalPlayedSeconds: $totalPlayedSeconds,
            batchId: $batchId,
        );
    }

    /**
     * A track was skipped. This is the feedback the station listens to most.
     */
    public function rotorStationFeedbackSkip(
        string|Id $station,
        string|int $trackId,
        int|float $totalPlayedSeconds,
        ?string $batchId = null,
        ?float $timestamp = null,
    ): bool {
        return $this->rotorStationFeedback(
            $station,
            FeedbackType::Skip,
            $timestamp,
            trackId: $trackId,
            totalPlayedSeconds: $totalPlayedSeconds,
            batchId: $batchId,
        );
    }

    /**
     * A station as the endpoints name it: `kind:tag`.
     */
    private static function stationTag(string|Id $station): string
    {
        return $station instanceof Id ? $station->tag() : $station;
    }

    /**
     * A setting as it goes on the wire, whether it came typed or bare.
     */
    private static function wireValue(string|\BackedEnum|null $value): ?string
    {
        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }

        return $value;
    }
}
