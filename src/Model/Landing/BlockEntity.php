<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\GeneratedPlaylist;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;

/**
 * One item inside a block of the front page.
 *
 * What $data holds depends on $type, which is the only thing that says so —
 * the same arrangement as a search's best match.
 */
final class BlockEntity extends Model
{
    /**
     * @var array<string, class-string<Model>>
     */
    private const TYPES = [
        'personal-playlist' => GeneratedPlaylist::class,
        'promotion' => Promotion::class,
        'album' => Album::class,
        'playlist' => Playlist::class,
        'chart-item' => ChartItem::class,
        'play-context' => PlayContext::class,
        'mix-link' => MixLink::class,
    ];

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
        public readonly Album|ChartItem|GeneratedPlaylist|MixLink|PlayContext|Playlist|Promotion|null $data = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * An entity of a kind this library does not know keeps its type and drops
     * its data, rather than guessing at a model and throwing.
     */
    protected static function prepare(array $args, array $data, ?Client $client): array
    {
        $type = $data['type'] ?? null;
        $model = is_string($type) ? (self::TYPES[$type] ?? null) : null;

        $args['data'] = null === $model ? null : $model::fromApi($data['data'] ?? null, $client);

        return $args;
    }

    protected function identity(): array
    {
        return [$this->id, $this->type];
    }
}
