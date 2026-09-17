<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Queue;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A queue as the listing shows it — enough to choose one, without its tracks.
 */
final class QueueItem extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'context' => [Context::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?Context $context = null,
        public readonly ?string $modified = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id];
    }
}
