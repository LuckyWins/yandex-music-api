<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Wave;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What a wave agent stands for. Only its kind is ever sent.
 */
final class WaveAgentEntity extends Model
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->type];
    }
}
