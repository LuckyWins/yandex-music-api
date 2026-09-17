<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Metatag;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A tag's name, short and long.
 */
final class MetatagTitle extends Model
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $fullTitle = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->title, $this->fullTitle];
    }
}
