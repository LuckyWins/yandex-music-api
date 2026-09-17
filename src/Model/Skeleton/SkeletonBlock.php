<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Skeleton;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One block of a page laid out by the service.
 *
 * A skeleton is what an app draws before the contents arrive: the shape of the
 * page, with a source to fetch each part from.
 */
final class SkeletonBlock extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'data' => [SkeletonBlockData::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $type = null,
        public readonly ?SkeletonBlockData $data = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->type];
    }
}
