<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A setting that slides between two ends rather than picking from a list.
 */
final class DiscreteScale extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'min' => [Value::class, 'one'],
        'max' => [Value::class, 'one'],
    ];

    public function __construct(
        public readonly string $type,
        public readonly string $name,
        public readonly ?Value $min = null,
        public readonly ?Value $max = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->type, $this->name];
    }
}
