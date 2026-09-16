<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * Everyone credited on a recording.
 */
final class Credits extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'credits' => [Credit::class, 'list'],
    ];

    public function __construct(
        /** @var list<Credit> */
        public readonly array $credits = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->credits];
    }
}
