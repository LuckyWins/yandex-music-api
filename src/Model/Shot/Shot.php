<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Shot;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * An interjection scheduled to play after a track.
 */
final class Shot extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'shotData' => [ShotData::class, 'one'],
    ];

    public function __construct(
        public readonly int $order,
        public readonly bool $played,
        public readonly string $shotId,
        public readonly string $status,
        public readonly ?ShotData $shotData = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->shotId];
    }
}
