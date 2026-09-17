<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistId;

/**
 * One block of the front page: a heading and the things under it.
 */
final class Block extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'entities' => [BlockEntity::class, 'list'],
        'playContext' => [PlaylistId::class, 'one'],
    ];

    /** @var array<string, class-string<Model>> */
    private const DATA_TYPES = [
        'personal-playlists' => PersonalPlaylistsData::class,
        'play-contexts' => PlayContextsData::class,
    ];

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
        public readonly ?string $typeForFrom = null,
        public readonly ?string $title = null,
        /** @var list<BlockEntity> */
        public readonly array $entities = [],
        public readonly ?string $description = null,
        public readonly PersonalPlaylistsData|PlayContextsData|null $data = null,
        /** The playlist this block plays when it is played as a whole. */
        public readonly ?PlaylistId $playContext = null,
        public readonly ?string $backgroundImageUrl = null,
        public readonly ?string $backgroundVideoUrl = null,
        public readonly ?string $backgroundVideoId = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Two kinds of block carry something beside their entities, and which is
     * which is decided by the block's own type.
     */
    protected static function prepare(array $args, array $data, ?Client $client): array
    {
        $type = $data['type'] ?? null;
        $model = is_string($type) ? (self::DATA_TYPES[$type] ?? null) : null;

        $args['data'] = null === $model ? null : $model::fromApi($data['data'] ?? null, $client);

        return $args;
    }

    protected function identity(): array
    {
        return [$this->id, $this->type];
    }
}
