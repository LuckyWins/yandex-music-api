<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\LicenceTextPart;

/**
 * A subscription plan on offer.
 */
final class Product extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'price' => [Price::class, 'one'],
        'introPrice' => [Price::class, 'one'],
        'startPrice' => [Price::class, 'one'],
        'licenceTextParts' => [LicenceTextPart::class, 'list'],
    ];

    public function __construct(
        public readonly string $productId,
        public readonly string $type,
        public readonly int $duration,
        public readonly int $trialDuration,
        public readonly string $feature,
        public readonly bool $debug,
        public readonly bool $plus,
        public readonly ?Price $price = null,
        public readonly ?string $commonPeriodDuration = null,
        public readonly ?bool $cheapest = null,
        public readonly ?string $title = null,
        public readonly ?bool $familySub = null,
        public readonly ?string $fbImage = null,
        public readonly ?string $fbName = null,
        public readonly ?bool $family = null,
        /**
         * Null when absent, unlike paymentMethodTypes below, which is empty.
         * The reference draws the same distinction; do not unify them.
         *
         * @var list<string>|null
         */
        public readonly ?array $features = null,
        public readonly ?string $description = null,
        public readonly ?bool $available = null,
        public readonly ?bool $trialAvailable = null,
        public readonly ?string $trialPeriodDuration = null,
        public readonly ?string $introPeriodDuration = null,
        public readonly ?Price $introPrice = null,
        public readonly ?string $startPeriodDuration = null,
        public readonly ?Price $startPrice = null,
        public readonly ?bool $vendorTrialAvailable = null,
        public readonly ?string $buttonText = null,
        public readonly ?string $buttonAdditionalText = null,
        /** @var list<LicenceTextPart> */
        public readonly array $licenceTextParts = [],
        /** @var list<string> */
        public readonly array $paymentMethodTypes = [],
        /** Which slot this offer occupied in the batch it was served in. */
        public readonly ?string $offersPositionId = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->productId, $this->type, $this->duration, $this->trialDuration, $this->feature, $this->debug];
    }
}
