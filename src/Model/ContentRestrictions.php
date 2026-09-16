<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * Why something may not be playable here.
 */
final class ContentRestrictions extends Model
{
    public function __construct(
        public readonly ?bool $available = null,
        /** @var list<string>|null */
        public readonly ?array $disclaimers = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->available, $this->disclaimers];
    }
}
