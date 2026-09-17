<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Skeleton;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Where a block's contents come from, and how much of it there is.
 */
final class SkeletonSource extends Model
{
    public function __construct(
        public readonly ?string $uri = null,
        public readonly ?int $count = null,
        public readonly ?int $countWeb = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->uri];
    }
}
