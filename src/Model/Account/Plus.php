<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Whether the account has Yandex Plus, the subscription the music service sits under.
 */
final class Plus extends Model
{
    public function __construct(
        public readonly bool $hasPlus,
        public readonly bool $isTutorialCompleted,
        /** Only ever seen null, so its type is a reading of the name. */
        public readonly ?bool $migrated = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->hasPlus, $this->isTutorialCompleted];
    }
}
