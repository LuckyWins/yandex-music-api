<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Wave;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The face a wave is presented under.
 */
final class WaveAgent extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'cover' => [Cover::class, 'one'],
        'entity' => [WaveAgentEntity::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $animationUri = null,
        public readonly ?Cover $cover = null,
        public readonly ?WaveAgentEntity $entity = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->animationUri, $this->entity];
    }
}
