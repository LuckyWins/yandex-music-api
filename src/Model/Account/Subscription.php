<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Everything about what the account is currently paying for.
 *
 * Mind the two pairs of similar names: nonAutoRenewableRemainder is how many
 * days are left, nonAutoRenewable is the window itself; autoRenewable and
 * familyAutoRenewable hold the same kind of thing for different payers.
 */
final class Subscription extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'nonAutoRenewableRemainder' => [RenewableRemainder::class, 'one'],
        'autoRenewable' => [AutoRenewable::class, 'list'],
        'familyAutoRenewable' => [AutoRenewable::class, 'list'],
        'operator' => [Operator::class, 'list'],
        'nonAutoRenewable' => [NonAutoRenewable::class, 'one'],
    ];

    public function __construct(
        /**
         * Optional despite the reference declaring it required: the radio's
         * own view of the account omits it, and a subscription that cannot be
         * deserialized takes the whole status with it.
         */
        public readonly ?bool $hadAnySubscription = null,
        public readonly ?RenewableRemainder $nonAutoRenewableRemainder = null,
        /** @var list<AutoRenewable> */
        public readonly array $autoRenewable = [],
        /** @var list<AutoRenewable> */
        public readonly array $familyAutoRenewable = [],
        /**
         * Operator-billed subscriptions. Singular in the API, plural in content.
         *
         * @var list<Operator>
         */
        public readonly array $operator = [],
        public readonly ?NonAutoRenewable $nonAutoRenewable = null,
        public readonly ?bool $canStartTrial = null,
        public readonly ?bool $mcdonalds = null,
        public readonly ?string $end = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->nonAutoRenewableRemainder, $this->autoRenewable, $this->familyAutoRenewable];
    }
}
