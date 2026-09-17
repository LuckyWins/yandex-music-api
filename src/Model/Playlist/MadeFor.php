<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Who a generated playlist was made for.
 */
final class MadeFor extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'userInfo' => [User::class, 'one'],
        'caseForms' => [CaseForms::class, 'one'],
    ];

    public function __construct(
        public readonly ?User $userInfo = null,
        public readonly ?CaseForms $caseForms = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->userInfo];
    }
}
