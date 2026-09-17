<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Search;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What to offer someone who has typed part of a query.
 */
final class Suggestions extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'best' => [Best::class, 'one'],
    ];

    public function __construct(
        public readonly ?Best $best = null,
        /** @var list<string> */
        public readonly array $suggestions = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->best, $this->suggestions];
    }
}
