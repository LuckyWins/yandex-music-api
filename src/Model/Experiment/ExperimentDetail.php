<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Experiment;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One A/B experiment the account has been placed into.
 */
final class ExperimentDetail extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'value' => [ExperimentDetailValue::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $group = null,
        public readonly ?ExperimentDetailValue $value = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->group, $this->value];
    }
}
