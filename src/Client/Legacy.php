<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

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
