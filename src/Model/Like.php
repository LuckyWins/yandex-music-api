<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;

/**
 * One entry in a user's likes: what was liked, and when.
 *
 * The liked object sits in whichever of $album, $artist or $playlist matches
 * $type; the other two are null. Tracks are not here — the API answers those
 * with a TracksList instead.
 *
 * Build these with listOfType() rather than fromApi(): the response says
 * nothing about what kind of thing it holds, so the type has to come from the
 * endpoint that was called.
 */
final class Like extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'album' => [Album::class, 'one'],
        'artist' => [Artist::class, 'one'],
        'playlist' => [Playlist::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $type = null,
        public readonly string|int|null $id = null,
        public readonly ?string $timestamp = null,
        public readonly ?Album $album = null,
        public readonly ?Artist $artist = null,
        public readonly ?Playlist $playlist = null,
        public readonly ?string $shortDescription = null,
        public readonly ?string $description = null,
        public readonly ?bool $isPremiere = null,
        public readonly ?bool $isBanner = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Build the likes an endpoint answered with, stamping each one with the
     * kind of thing it holds.
     *
     * Two shapes arrive. Usually the liked object is wrapped, under a key
     * named after its type. Liked artists are the exception: they come as bare
     * artists, with no wrapper and no timestamp, and are treated as such.
     *
     * The reference library solves this by giving `de_json` an extra argument;
     * here the type stays out of the shared deserializer and lives in this
     * named factory instead.
     *
     * @return list<self>
     */
    public static function listOfType(mixed $data, string $type, ?Client $client = null): array
    {
        if (!is_array($data)) {
            return [];
        }

        $result = [];

        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }

            $like = array_key_exists($type, $item)
                ? self::fromApi(array_merge($item, ['type' => $type]), $client)
                : self::unwrapped($item, $type, $client);

            if (null !== $like) {
                $result[] = $like;
            }
        }

        return $result;
    }

    /**
     * Build a like from the bare form, where the response is the liked object
     * itself rather than a wrapper around it.
     *
     * The object's own fields are left to the object; the ones that belong to
     * the like — so far only the timestamp — are lifted out first, so they do
     * not end up reported as fields the artist model does not know.
     *
     * @param array<array-key, mixed> $item
     */
    private static function unwrapped(array $item, string $type, ?Client $client): ?self
    {
        $timestamp = $item['timestamp'] ?? null;
        unset($item['timestamp']);

        return self::fromApi([
            'type' => $type,
            'timestamp' => $timestamp,
            $type => $item,
        ], $client);
    }

    /**
     * The liked object itself, whichever kind it is.
     */
    public function object(): Album|Artist|Playlist|null
    {
        return $this->album ?? $this->artist ?? $this->playlist;
    }

    protected function identity(): array
    {
        return [$this->type, $this->id, $this->object()];
    }
}
