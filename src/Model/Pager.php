<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * Where a paged response sits in the whole of what there is.
 */
final class Pager extends Model
{
    public function __construct(
        public readonly int $total,
        public readonly int $page,
        public readonly int $perPage,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->total, $this->page, $this->perPage];
    }
}
