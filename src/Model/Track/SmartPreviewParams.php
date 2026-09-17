<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * How to cut a short preview out of a track.
 */
final class SmartPreviewParams extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'fade' => [Fade::class, 'one'],
    ];

    public function __construct(
        public readonly ?int $durationMs = null,
        public readonly ?Fade $fade = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->durationMs, $this->fade];
    }
}
