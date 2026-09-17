<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Whether a playlist was generated for the account reading it, and that
 * person's name in every case so a heading can be built around it.
 *
 * Close to MadeFor but not the same shape: this one carries a flag instead of
 * the user, and the reference library does not model it at all.
 */
final class MadeForUser extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'caseForms' => [CaseForms::class, 'one'],
    ];

    public function __construct(
        public readonly ?bool $isMadeForUser = null,
        public readonly ?CaseForms $caseForms = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->isMadeForUser, $this->caseForms];
    }
}
