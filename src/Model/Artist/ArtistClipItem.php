<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One entry in an artist's clips, wrapped the way the landing wraps its own —
 * a type beside the thing itself.
 *
 * Only `clip` has been seen so far; another type leaves $data empty rather
 * than guessing.
 */
final class ArtistClipItem extends Model
{
    /** @var array<string, class-string<Model>> */
    private const TYPES = [
        'clip' => ArtistClipData::class,
    ];

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?ArtistClipData $data = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected static function prepare(array $args, array $data, ?Client $client): array
    {
        $type = $data['type'] ?? null;
        $model = is_string($type) ? (self::TYPES[$type] ?? null) : null;

        $args['data'] = null === $model ? null : $model::fromApi($data['data'] ?? null, $client);

        return $args;
    }

    protected function identity(): array
    {
        return [$this->type, $this->data];
    }
}
