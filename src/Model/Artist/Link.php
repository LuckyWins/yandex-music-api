<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A link from an artist's page — their site, or a social account.
 */
final class Link extends Model
{
    public function __construct(
        public readonly string $title,
        public readonly string $href,
        public readonly string $type,
        public readonly ?string $socialNetwork = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->title, $this->href, $this->type];
    }
}
