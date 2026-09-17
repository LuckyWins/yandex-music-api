<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One allowed setting: what to send, and what to call it in an interface.
 */
final class Value extends Model
{
    public function __construct(
        public readonly string $value,
        public readonly string $name,
        /**
         * The three below arrive only in `restrictions2`, the newer
         * arrangement: artwork for the value, whether it stands for "not
         * chosen", and the seed that selects it elsewhere in the service.
         */
        public readonly ?string $imageUrl = null,
        public readonly ?bool $unspecified = null,
        public readonly ?string $serializedSeed = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->value];
    }
}
