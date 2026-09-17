<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Pin\Pin;
use LuckyWins\YandexMusic\Model\Pin\PinsList;

/**
 * Pins — what the account keeps at the top of its front page.
 *
 * Four kinds can be pinned, each with a path of its own: albums, artists,
 * playlists and waves. Pinning is a PUT and unpinning a DELETE, both naming
 * the thing in a JSON body rather than in the path.
 */
trait Pins
{
    /**
     * Everything pinned, in the order it is shown.
     */
    public function pins(): ?PinsList
    {
        return PinsList::fromApi($this->request->get($this->getBaseUrl().'/pins'), $this);
    }

    public function pinAlbum(string|int $albumId): ?Pin
    {
        return $this->pin('album', ['id' => $albumId]);
    }

    public function unpinAlbum(string|int $albumId): bool
    {
        return $this->unpin('album', ['id' => $albumId]);
    }

    public function pinArtist(string|int $artistId): ?Pin
    {
        return $this->pin('artist', ['id' => $artistId]);
    }

    public function unpinArtist(string|int $artistId): bool
    {
        return $this->unpin('artist', ['id' => $artistId]);
    }

    public function pinPlaylist(string|int $uid, string|int $kind): ?Pin
    {
        return $this->pin('playlist', ['uid' => $uid, 'kind' => $kind]);
    }

    public function unpinPlaylist(string|int $uid, string|int $kind): bool
    {
        return $this->unpin('playlist', ['uid' => $uid, 'kind' => $kind]);
    }

    /**
     * Pin a station.
     *
     * The seeds are what the wave is built from — `user:onyourwave`, a genre,
     * an artist. They go as a list even when there is one of them: sending a
     * bare string makes the server close the connection without answering,
     * which is what the reference library does and why its version of this
     * cannot work.
     *
     * @param string|list<string> $seeds
     */
    public function pinWave(string|array $seeds): ?Pin
    {
        return $this->pin('wave', ['seeds' => is_array($seeds) ? array_values($seeds) : [$seeds]]);
    }

    /**
     * @param string|list<string> $seeds
     */
    public function unpinWave(string|array $seeds): bool
    {
        return $this->unpin('wave', ['seeds' => is_array($seeds) ? array_values($seeds) : [$seeds]]);
    }

    /**
     * @param array<string, mixed> $body
     */
    private function pin(string $kind, array $body): ?Pin
    {
        return Pin::fromApi($this->request->putJson($this->getBaseUrl().'/pin/'.$kind, $body), $this);
    }

    /**
     * @param array<string, mixed> $body
     */
    private function unpin(string $kind, array $body): bool
    {
        return 'ok' === $this->request->deleteJson($this->getBaseUrl().'/pin/'.$kind, $body);
    }
}
