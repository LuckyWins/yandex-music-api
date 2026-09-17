<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Wave;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One entry in a list of things like the one you asked about.
 */
final class SimilarEntityItem extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'data' => [SimilarEntityData::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?SimilarEntityData $data = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->type, $this->data];
    }
}
