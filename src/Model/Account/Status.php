<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\StationData;

/**
 * The whole picture of an account: who it is, what it may do, what it pays for.
 *
 * This is what init() fetches and keeps on the client.
 */
final class Status extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'account' => [Account::class, 'one'],
        'permissions' => [Permissions::class, 'one'],
        'subscription' => [Subscription::class, 'one'],
        'plus' => [Plus::class, 'one'],
        'stationData' => [StationData::class, 'one'],
        'barBelow' => [Alert::class, 'one'],
    ];

    public function __construct(
        public readonly ?Account $account = null,
        public readonly ?Permissions $permissions = null,
        public readonly ?string $advertisement = null,
        public readonly ?Subscription $subscription = null,
        public readonly ?int $cacheLimit = null,
        public readonly ?bool $subeditor = null,
        public readonly ?int $subeditorLevel = null,
        public readonly ?Plus $plus = null,
        public readonly ?string $defaultEmail = null,
        public readonly ?int $skipsPerHour = null,
        public readonly ?bool $stationExists = null,
        public readonly ?StationData $stationData = null,
        /** The banner to show below the player, if Yandex wants one shown. */
        public readonly ?Alert $barBelow = null,
        public readonly ?int $premiumRegion = null,
        public readonly ?int $experiment = null,
        public readonly ?bool $pretrialActive = null,
        public readonly ?string $userhash = null,
        /**
         * Subscriptions across the wider Yandex ecosystem, not just music.
         * Undocumented and absent from the reference library; the shape is
         * `{activeSubscriptions, availableSubscriptions}` but is not modelled
         * yet, so it arrives raw.
         *
         * @var array<string, mixed>|null
         */
        public readonly ?array $masterhub = null,
        /**
         * Undocumented, absent from the reference library, and empty on the
         * accounts seen so far. Raw until there is something to model.
         *
         * @var list<mixed>|null
         */
        public readonly ?array $hasOptions = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->account, $this->permissions];
    }
}
