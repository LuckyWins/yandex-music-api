<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Pin;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One thing pinned to the top of the front page.
 *
 * $type says what it is — an album, an artist, a playlist or a wave — and
 * decides which fields of $data are filled.
 */
final class Pin extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'data' => [PinData::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?PinData $data = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->type, $this->data];
    }
}
