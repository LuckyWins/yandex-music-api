<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * Notices that must accompany a recording.
 */
final class Disclaimer extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'foreignAgent' => [ForeignAgent::class, 'one'],
    ];

    public function __construct(
        public readonly ?ForeignAgent $foreignAgent = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->foreignAgent];
    }
}
