<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The playlists filed under a tag, as references rather than playlists.
 *
 * Fetch them with playlistsList(), which takes exactly the pairs these carry.
 */
final class TagResult extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'ids' => [PlaylistId::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $tag = null,
        /** @var list<PlaylistId> */
        public readonly array $ids = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The references in the `{uid}:{kind}` form the batch endpoints take.
     *
     * @return list<string>
     */
    public function pairs(): array
    {
        $pairs = [];

        foreach ($this->ids as $id) {
            $pair = $id->pair();

            if (null !== $pair) {
                $pairs[] = $pair;
            }
        }

        return $pairs;
    }

    protected function identity(): array
    {
        return [$this->tag, $this->ids];
    }
}
