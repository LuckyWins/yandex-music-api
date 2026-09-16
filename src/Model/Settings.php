<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Account\Price;
use LuckyWins\YandexMusic\Model\Account\Product;

/**
 * What the account can be sold, and where to buy it.
 */
final class Settings extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'inAppProducts' => [Product::class, 'list'],
        'nativeProducts' => [Product::class, 'list'],
        'webPaymentMonthProductPrice' => [Price::class, 'one'],
    ];

    public function __construct(
        public readonly string $webPaymentUrl,
        public readonly bool $promoCodesEnabled,
        /** @var list<Product> */
        public readonly array $inAppProducts = [],
        /** @var list<Product> */
        public readonly array $nativeProducts = [],
        public readonly ?Price $webPaymentMonthProductPrice = null,
        /** Identifies the batch of offers this response was generated from. */
        public readonly ?string $offersBatchId = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->inAppProducts, $this->nativeProducts, $this->webPaymentUrl];
    }
}
