<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One entry in an artist's donation block, wrapped as a type beside its data.
 */
final class ArtistDonationItem extends Model
{
    /** @var array<string, class-string<Model>> */
    private const TYPES = [
        'artist-donation' => ArtistDonationData::class,
    ];

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?ArtistDonationData $data = null,
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
