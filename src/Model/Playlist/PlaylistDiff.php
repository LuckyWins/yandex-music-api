<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\Landing\TrackId;

/**
 * The set of operations that changes a playlist's contents.
 *
 * Not a Model: nothing deserializes into this. It travels the other way, as
 * the body of /users/{uid}/playlists/{kind}/change.
 *
 *     $diff = (new PlaylistDiff())
 *         ->insert(0, new TrackId(id: 31190260, albumId: 4243617))
 *         ->delete(3, 5);
 *
 * The reference builds the same body, but hands the client a JSON string and
 * trusts it; here the operations stay structured until the request is made,
 * so a malformed change is caught before it reaches the network.
 */
final class PlaylistDiff
{
    /** @var list<array<string, mixed>> */
    private array $operations = [];

    /**
     * Insert tracks at a position. Index 0 puts them at the top.
     *
     * Every track needs its album: the API identifies a track inside a
     * playlist by the pair, and refuses an insert that omits the album.
     */
    public function insert(int $at, TrackId ...$tracks): self
    {
        if ([] === $tracks) {
            throw new YandexMusicException('An insert operation needs at least one track.');
        }

        $payload = [];

        foreach ($tracks as $track) {
            if (null === $track->id || null === $track->albumId) {
                throw new YandexMusicException(
                    'Inserting a track into a playlist needs both its id and its album id.',
                );
            }

            $payload[] = ['id' => $track->id, 'albumId' => $track->albumId];
        }

        $this->operations[] = ['op' => 'insert', 'at' => $at, 'tracks' => $payload];

        return $this;
    }

    /**
     * Remove the tracks in a half-open range of positions: from $from up to
     * but not including $to.
     */
    public function delete(int $from, int $to): self
    {
        if ($from < 0 || $to < $from) {
            throw new YandexMusicException(sprintf(
                'A delete operation needs a non-negative ascending range, got %d..%d.',
                $from,
                $to,
            ));
        }

        $this->operations[] = ['op' => 'delete', 'from' => $from, 'to' => $to];

        return $this;
    }

    public function isEmpty(): bool
    {
        return [] === $this->operations;
    }

    /**
     * The operations as the request body wants them.
     */
    public function toJson(): string
    {
        if ($this->isEmpty()) {
            throw new YandexMusicException('A playlist change needs at least one operation.');
        }

        return json_encode($this->operations, JSON_THROW_ON_ERROR);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function operations(): array
    {
        return $this->operations;
    }
}
