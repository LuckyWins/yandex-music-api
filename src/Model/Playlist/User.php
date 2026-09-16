<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A Yandex.Music user.
 *
 * Which fields arrive depends on where the user came from: a playlist owner
 * carries most of them, the uploader of a track carries only identity and
 * names, and `regions` shows up in search results alone.
 */
final class User extends Model
{
    public function __construct(
        public readonly int $uid,
        public readonly string $login,
        public readonly ?string $name = null,
        public readonly ?string $displayName = null,
        public readonly ?string $fullName = null,
        public readonly ?string $sex = null,
        public readonly ?bool $verified = null,
        /** @var list<int> */
        public readonly array $regions = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->uid, $this->login];
    }
}
