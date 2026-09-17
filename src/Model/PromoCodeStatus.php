<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Account\Status;

/**
 * What came of redeeming a promo code.
 *
 * On success the account status is returned alongside, already updated.
 */
final class PromoCodeStatus extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'accountStatus' => [Status::class, 'one'],
    ];

    public function __construct(
        public readonly string $status,
        public readonly string $statusDesc,
        public readonly ?Status $accountStatus = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->status, $this->statusDesc, $this->accountStatus];
    }
}
