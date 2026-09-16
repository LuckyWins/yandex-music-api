<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The person behind the token.
 *
 * Carries the profile Yandex holds: names, birthday, and the phone numbers on
 * the Yandex ID. Treat it as personal data.
 */
final class Account extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'passportPhones' => [PassportPhone::class, 'list'],
    ];

    public function __construct(
        public readonly string $now,
        public readonly bool $serviceAvailable,
        public readonly ?int $region = null,
        public readonly ?int $uid = null,
        public readonly ?string $login = null,
        public readonly ?string $fullName = null,
        public readonly ?string $secondName = null,
        public readonly ?string $firstName = null,
        public readonly ?string $displayName = null,
        public readonly ?bool $hostedUser = null,
        public readonly ?string $birthday = null,
        /** @var list<PassportPhone> */
        public readonly array $passportPhones = [],
        public readonly ?string $registeredAt = null,
        public readonly ?bool $hasInfoForAppMetrica = null,
        public readonly ?bool $child = null,
        /** Two-letter country code, alongside the numeric `region`. */
        public readonly ?string $regionCode = null,
        /** True on a seat in someone else's family subscription. */
        public readonly ?bool $nonOwnerFamilyMember = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        // An account with no uid is an anonymous one, and two of those are not
        // the same account. Falling back to object identity says exactly that.
        return null === $this->uid ? [] : [$this->uid];
    }
}
