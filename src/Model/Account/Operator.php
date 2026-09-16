<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A subscription billed through a mobile operator.
 */
final class Operator extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'deactivation' => [Deactivation::class, 'list'],
    ];

    public function __construct(
        public readonly string $productId,
        public readonly string $phone,
        public readonly string $paymentRegularity,
        public readonly string $title,
        public readonly bool $suspended,
        /**
         * How to cancel. Singular in the API, plural in content.
         *
         * @var list<Deactivation>
         */
        public readonly array $deactivation = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->productId, $this->phone];
    }
}
