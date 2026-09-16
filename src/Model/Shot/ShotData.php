<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Shot;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The content of one of Alice's spoken interjections between tracks.
 */
final class ShotData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'shotType' => [ShotType::class, 'one'],
    ];

    public function __construct(
        public readonly string $coverUri,
        /** Where the audio lives. */
        public readonly string $mdsUrl,
        public readonly string $shotText,
        public readonly ?ShotType $shotType = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->coverUri, $this->mdsUrl, $this->shotText];
    }
}
