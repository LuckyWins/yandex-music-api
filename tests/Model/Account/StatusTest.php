<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\Account;
use LuckyWins\YandexMusic\Model\Account\Alert;
use LuckyWins\YandexMusic\Model\Account\AutoRenewable;
use LuckyWins\YandexMusic\Model\Account\Permissions;
use LuckyWins\YandexMusic\Model\Account\Plus;
use LuckyWins\YandexMusic\Model\Account\Status;
use LuckyWins\YandexMusic\Model\Account\Subscription;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\StationData;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * The deepest tree in the library, and the real test of nested deserialization:
 * Status -> Subscription -> AutoRenewable -> Product -> LicenceTextPart is six
 * levels, across four namespaces.
 */
#[CoversClass(Status::class)]
final class StatusTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Status::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'account' => [
                'now' => '2026-09-16T12:00:00+00:00',
                'serviceAvailable' => true,
                'uid' => 1130000002804451,
                'login' => 'user@yandex.ru',
                'passportPhones' => [['phone' => '+79001234567']],
            ],
            'permissions' => [
                'until' => '2026-12-31T00:00:00+00:00',
                'values' => ['feed-play', 'full'],
                'default' => ['landing-play'],
            ],
            'subscription' => [
                'hadAnySubscription' => true,
                'nonAutoRenewableRemainder' => ['days' => 12],
                'autoRenewable' => [[
                    'expires' => '2026-10-16T12:00:00+00:00',
                    'vendor' => 'AppStore',
                    'vendorHelpUrl' => 'https://support.apple.com',
                    'finished' => false,
                    'product' => [
                        'productId' => 'ru.yandex.mobile.music.1month',
                        'type' => 'subscription',
                        'duration' => 30,
                        'trialDuration' => 7,
                        'feature' => 'music-and-plus',
                        'debug' => false,
                        'plus' => true,
                        'licenceTextParts' => [['text' => 'Условия', 'url' => 'https://yandex.ru/legal/']],
                    ],
                    'masterInfo' => ['uid' => 999, 'login' => 'owner@yandex.ru'],
                ]],
                'operator' => [[
                    'productId' => 'mts.monthly',
                    'phone' => '+79001234567',
                    'paymentRegularity' => 'Ежемесячно',
                    'title' => 'МТС',
                    'suspended' => false,
                    'deactivation' => [['method' => 'ussd']],
                ]],
            ],
            'plus' => ['hasPlus' => true, 'isTutorialCompleted' => true],
            'stationData' => ['name' => 'Моя волна'],
            'barBelow' => [
                'alertId' => 'xxx',
                'text' => 'Подписка заканчивается',
                'bgColor' => '#ffcc00',
                'textColor' => '#000000',
                'alertType' => 'subscription',
            ],
            'advertisement' => 'Первый месяц бесплатно',
            'cacheLimit' => 99,
            'subeditor' => false,
            'subeditorLevel' => 0,
            'defaultEmail' => 'user@yandex.ru',
            'skipsPerHour' => 6,
            'stationExists' => true,
            'premiumRegion' => 225,
            'experiment' => 109,
            'pretrialActive' => false,
            'userhash' => '2a1d970ce4dadc3333280aa8727d1c41',
            'masterhub' => ['activeSubscriptions' => [], 'availableSubscriptions' => []],
            'hasOptions' => [],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['account' => ['now' => '2026-09-16T12:00:00+00:00', 'serviceAvailable' => true]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Status::class, $model);

        self::assertInstanceOf(Account::class, $model->account);
        self::assertSame(1130000002804451, $model->account->uid);
        self::assertSame('+79001234567', $model->account->passportPhones[0]->phone);

        self::assertInstanceOf(Permissions::class, $model->permissions);
        self::assertSame(['feed-play', 'full'], $model->permissions->values);

        self::assertInstanceOf(Plus::class, $model->plus);
        self::assertTrue($model->plus->hasPlus);

        self::assertInstanceOf(StationData::class, $model->stationData);
        self::assertSame('Моя волна', $model->stationData->name);

        // The key is barBelow, the model is Alert — names deliberately differ.
        self::assertInstanceOf(Alert::class, $model->barBelow);
        self::assertSame('Подписка заканчивается', $model->barBelow->text);

        self::assertSame(99, $model->cacheLimit);
        self::assertSame(6, $model->skipsPerHour);
        self::assertSame('2a1d970ce4dadc3333280aa8727d1c41', $model->userhash);

        // Undocumented and unmodelled, so they arrive as they were sent.
        self::assertSame(['activeSubscriptions' => [], 'availableSubscriptions' => []], $model->masterhub);
        self::assertSame([], $model->hasOptions);

        $this->assertSubscriptionTree($model->subscription);
    }

    protected function equalityTriple(): array
    {
        $account = new Account('now', true, uid: 1);
        $other = new Account('now', true, uid: 2);
        $permissions = new Permissions('until', ['a'], ['b']);

        return [
            new Status($account, $permissions),
            new Status($account, $permissions, advertisement: 'ignored'),
            new Status($other, $permissions),
        ];
    }

    /**
     * Walk the whole way down, because the point of this model is the depth.
     */
    private function assertSubscriptionTree(?Subscription $subscription): void
    {
        self::assertInstanceOf(Subscription::class, $subscription);
        self::assertTrue($subscription->hadAnySubscription);
        self::assertSame(12, $subscription->nonAutoRenewableRemainder?->days);

        self::assertCount(1, $subscription->autoRenewable);
        $renewable = $subscription->autoRenewable[0];
        self::assertInstanceOf(AutoRenewable::class, $renewable);
        self::assertSame('AppStore', $renewable->vendor);
        self::assertSame(999, $renewable->masterInfo?->uid);

        $product = $renewable->product;
        self::assertNotNull($product);
        self::assertSame(30, $product->duration);
        self::assertSame('https://yandex.ru/legal/', $product->licenceTextParts[0]->url);

        self::assertCount(1, $subscription->operator);
        self::assertSame('ussd', $subscription->operator[0]->deactivation[0]->method);

        // Not sent, so empty rather than null.
        self::assertSame([], $subscription->familyAutoRenewable);
        self::assertNull($subscription->nonAutoRenewable);
    }
}
