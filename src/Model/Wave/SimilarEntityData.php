<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Wave;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What a similar entity actually is, once its type has said which half of
 * this to read.
 */
final class SimilarEntityData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'wave' => [Wave::class, 'one'],
        'agent' => [WaveAgent::class, 'one'],
    ];

    public function __construct(
        public readonly ?Wave $wave = null,
        public readonly ?WaveAgent $agent = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->wave, $this->agent];
    }
}
