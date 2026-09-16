<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Shot;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What kind of interjection a shot is.
 */
final class ShotType extends Model
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id];
    }
}
