<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use DateTimeImmutable;
use DateTimeInterface;
use LuckyWins\YandexMusic\Model\Account\Status;

/**
 * Endpoints carried over from the 2019 library, not yet converted to models.
 *
 * Everything here returns raw decoded data — nested arrays, straight from the
 * API — so callers have to know the response shape themselves. Each stage of
 * the modernization lifts one domain out of this trait into a typed one; when
 * the trait is empty, the port is finished.
 *
 * Note that these return arrays, where the 2019 library returned stdClass. The
 * model layer works in arrays, and carrying two decoding modes would be worse
 * than the one-off break.
 */
trait Legacy
{
    // -- Landing and feed ---------------------------------------------------

    /** @return array<string, mixed> */
    public function feed(): array
    {
        return $this->getArray('/feed');
    }

    public function feedWizardIsPassed(): mixed
    {
        return $this->request->get($this->getBaseUrl().'/feed/wizard/is-passed');
    }

    /**
     * Blocks understood by the endpoint: personalplaylists, promotions,
     * new-releases, new-playlists, mixes, chart, artists, albums, playlists,
     * play_contexts.
     *
     * @param list<string>|string $blocks
     *
     * @return array<string, mixed>
     */
    public function landing(array|string $blocks): array
    {
        $blocks = is_array($blocks) ? implode(',', $blocks) : $blocks;

        return $this->getArray('/landing3', ['blocks' => $blocks]);
    }

    /** @return array<string, mixed> */
    public function genres(): array
    {
        return $this->getArray('/genres');
    }

    // -- Search -------------------------------------------------------------

    // -- Radio --------------------------------------------------------------

    /**
     * The account as radio sees it — the same model, with a few extra fields
     * filled in such as how many skips per hour are left.
     */
    public function rotorAccountStatus(): ?Status
    {
        return Status::fromApi($this->request->get($this->getBaseUrl().'/rotor/account/status'), $this);
    }

    /** @return array<string, mixed> */
    public function rotorStationsDashboard(): array
    {
        return $this->getArray('/rotor/stations/dashboard');
    }

    /**
     * @param string $language response language, ISO 639-1
     */
    public function rotorStationsList(string $language = 'en'): mixed
    {
        return $this->request->get($this->getBaseUrl().'/rotor/stations/list', ['language' => $language]);
    }

    public function rotorStationGenreFeedback(
        string $genre,
        string $type,
        ?string $from = null,
        string|int|null $batchId = null,
        string|int|null $trackId = null,
    ): mixed {
        $url = $this->getBaseUrl().'/rotor/station/genre:'.$genre.'/feedback';

        if (null !== $batchId) {
            $url .= '?'.http_build_query(['batch-id' => $batchId]);
        }

        $data = [
            'type' => $type,
            'timestamp' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
        ];

        if (null !== $from) {
            $data['from'] = $from;
        }

        if (null !== $trackId) {
            $data['trackId'] = $trackId;
        }

        return $this->request->post($url, $data);
    }

    public function rotorStationGenreFeedbackRadioStarted(string $genre, string $from): mixed
    {
        return $this->rotorStationGenreFeedback($genre, 'radioStarted', $from);
    }

    public function rotorStationGenreFeedbackTrackStarted(string $genre, string $from): mixed
    {
        return $this->rotorStationGenreFeedback($genre, 'trackStarted', $from);
    }

    public function rotorStationGenreInfo(string $genre): mixed
    {
        return $this->request->get($this->getBaseUrl().'/rotor/station/genre:'.$genre.'/info');
    }

    public function rotorStationGenreTracks(string $genre): mixed
    {
        return $this->request->get($this->getBaseUrl().'/rotor/station/genre:'.$genre.'/tracks');
    }

    // -- Batch lookups ------------------------------------------------------

    // -- Likes --------------------------------------------------------------

    // -- Dislikes -----------------------------------------------------------

    // -- Internals ----------------------------------------------------------

    /**
     * @param array<string, scalar|null> $params
     *
     * @return array<string, mixed>
     */
    private function getArray(string $path, array $params = []): array
    {
        $result = $this->request->get($this->getBaseUrl().$path, $params);

        return is_array($result) ? $result : [];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function postArray(string $path, array $data = []): array
    {
        $result = $this->request->post($this->getBaseUrl().$path, $data);

        return is_array($result) ? $result : [];
    }

}
