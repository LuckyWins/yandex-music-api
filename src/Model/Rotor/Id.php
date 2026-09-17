<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What identifies a station: a kind and a tag, such as `genre` and `allrock`.
 *
 * The endpoints want the two joined with a colon, which is what tag() gives.
 */
final class Id extends Model
{
    public function __construct(
        public readonly string $type,
        public readonly string $tag,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The form the endpoints take: `genre:allrock`, `user:onyourwave`.
     */
    public function tag(): string
    {
        return $this->type.':'.$this->tag;
    }

    protected function identity(): array
    {
        return [$this->type, $this->tag];
    }
}
