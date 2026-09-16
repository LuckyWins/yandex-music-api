<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\User;

/**
 * A subscription that renews itself until cancelled.
 */
final class AutoRenewable extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'product' => [Product::class, 'one'],
        'masterInfo' => [User::class, 'one'],
    ];

    public function __construct(
        public readonly string $expires,
        public readonly string $vendor,
        public readonly string $vendorHelpUrl,
        public readonly bool $finished,
        public readonly ?Product $product = null,
        /** Whose family subscription this seat belongs to. */
        public readonly ?User $masterInfo = null,
        public readonly ?string $productId = null,
        public readonly ?int $orderId = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->expires, $this->vendor, $this->vendorHelpUrl, $this->product, $this->finished];
    }
}
