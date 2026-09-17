<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The wrapper the tab configuration arrives in.
 */
final class ConcertTabConfig extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'config' => [ConcertTabConfigData::class, 'one'],
    ];

    public function __construct(
        public readonly ?ConcertTabConfigData $config = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->config];
    }
}
