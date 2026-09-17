<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\MusicHistory;

use LuckyWins\YandexMusic\Exception\YandexMusicException;

/**
 * What to ask the history about.
 *
 * Not a Model: nothing deserializes into this, it only travels outward — the
 * same arrangement as PlaylistDiff.
 *
 *     $what = (new MusicHistoryQuery())
 *         ->track(31190260, 4243617)
 *         ->album(4243617)
 *         ->playlist(503646255, 1042);
 *
 * The reference takes five parallel lists instead, two of them lists of pairs.
 * PHP has no tuples, and a parameter typed
 * `list<array{0: string|int, 1: string|int}>` reads like nothing at all.
 */
final class MusicHistoryQuery
{
    /** @var list<array{type: string, data: array{itemId: array<string, mixed>}}> */
    private array $items = [];

    /**
     * A track, which is only identified together with its album.
     */
    public function track(string|int $trackId, string|int $albumId): self
    {
        return $this->add('track', ['trackId' => (string) $trackId, 'albumId' => (string) $albumId]);
    }

    public function album(string|int $albumId): self
    {
        return $this->add('album', ['id' => (string) $albumId]);
    }

    public function artist(string|int $artistId): self
    {
        return $this->add('artist', ['id' => (string) $artistId]);
    }

    public function playlist(string|int $uid, string|int $kind): self
    {
        return $this->add('playlist', ['uid' => (int) $uid, 'kind' => (int) $kind]);
    }

    /**
     * A station, named by what it is built from.
     *
     * @param string|list<string> $seeds
     */
    public function wave(string|array $seeds): self
    {
        return $this->add('wave', ['seeds' => is_array($seeds) ? array_values($seeds) : [$seeds]]);
    }

    public function isEmpty(): bool
    {
        return [] === $this->items;
    }

    /**
     * The body the endpoint wants.
     *
     * @return array{items: list<array{type: string, data: array{itemId: array<string, mixed>}}>}
     */
    public function toArray(): array
    {
        if ($this->isEmpty()) {
            throw new YandexMusicException('Asking the history about nothing at all is not a question.');
        }

        return ['items' => $this->items];
    }

    /**
     * @param array<string, mixed> $itemId
     */
    private function add(string $type, array $itemId): self
    {
        $this->items[] = ['type' => $type, 'data' => ['itemId' => $itemId]];

        return $this;
    }
}
