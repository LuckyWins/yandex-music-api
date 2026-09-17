<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What the personal-playlists block knows beyond its entities: whether the
 * account has answered the taste wizard, which decides how good they are.
 */
final class PersonalPlaylistsData extends Model
{
    public function __construct(
        public readonly ?bool $isWizardPassed = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->isWizardPassed];
    }
}
