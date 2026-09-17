<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Search;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\Video;

/**
 * The single best match for a query, whatever kind of thing that turned out
 * to be. $type says which, and $result holds it.
 */
final class Best extends Model
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly Album|Artist|Playlist|Track|User|Video|null $result = null,
        public readonly ?string $text = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Unlike everything else, what to build is decided by the response rather
     * than by the field it arrived in. An unknown type leaves $result null
     * rather than guessing.
     */
    protected static function prepare(array $args, array $data, ?Client $client): array
    {
        $type = $data['type'] ?? null;
        $model = is_string($type) ? SearchType::tryFrom($type)?->model() : null;

        $args['result'] = null === $model ? null : $model::fromApi($data['result'] ?? null, $client);

        return $args;
    }

    protected function identity(): array
    {
        return [$this->type, $this->result];
    }
}
