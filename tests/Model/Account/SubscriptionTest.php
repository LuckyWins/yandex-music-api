<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\AutoRenewable;
use LuckyWins\YandexMusic\Model\Account\NonAutoRenewable;
use LuckyWins\YandexMusic\Model\Account\Operator;
use LuckyWins\YandexMusic\Model\Account\RenewableRemainder;
use LuckyWins\YandexMusic\Model\Account\Subscription;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Subscription::class)]
final class SubscriptionTest extends ModelTestCase
{
    private const RENEWABLE = [
        'expires' => '2026-10-16T12:00:00+00:00',
        'vendor' => 'AppStore',
        'vendorHelpUrl' => 'https://support.apple.com',
        'finished' => false,
    ];

    protected static function modelClass(): string
    {
        return Subscription::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'hadAnySubscription' => true,
            'nonAutoRenewableRemainder' => ['days' => 12],
            'autoRenewable' => [self::RENEWABLE],
            'familyAutoRenewable' => [self::RENEWABLE],
            'operator' => [[
                'productId' => 'mts.monthly',
                'phone' => '+79001234567',
                'paymentRegularity' => 'Ежемесячно',
                'title' => 'МТС',
                'suspended' => false,
            ]],
            'nonAutoRenewable' => ['start' => '2026-01-01', 'end' => '2026-02-01'],
            'canStartTrial' => false,
            'mcdonalds' => false,
            'end' => '2026-10-16T12:00:00+00:00',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['hadAnySubscription' => false];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Subscription::class, $model);
        self::assertTrue($model->hadAnySubscription);

        // Two similarly named fields holding different models.
        self::assertInstanceOf(RenewableRemainder::class, $model->nonAutoRenewableRemainder);
        self::assertSame(12, $model->nonAutoRenewableRemainder->days);
        self::assertInstanceOf(NonAutoRenewable::class, $model->nonAutoRenewable);
        self::assertSame('2026-02-01', $model->nonAutoRenewable->end);

        // Two different keys resolving to the same model.
        self::assertCount(1, $model->autoRenewable);
        self::assertCount(1, $model->familyAutoRenewable);
        self::assertInstanceOf(AutoRenewable::class, $model->autoRenewable[0]);
        self::assertInstanceOf(AutoRenewable::class, $model->familyAutoRenewable[0]);

        self::assertCount(1, $model->operator);
        self::assertInstanceOf(Operator::class, $model->operator[0]);
        self::assertSame('МТС', $model->operator[0]->title);
    }

    protected function equalityTriple(): array
    {
        $remainder = new RenewableRemainder(12);

        return [
            new Subscription(true, $remainder),
            new Subscription(false, $remainder),
            new Subscription(true, new RenewableRemainder(3)),
        ];
    }

    public function testEveryListDefaultsToEmpty(): void
    {
        $model = Subscription::fromApi(self::requiredPayload(), self::client());

        self::assertInstanceOf(Subscription::class, $model);
        self::assertSame([], $model->autoRenewable);
        self::assertSame([], $model->familyAutoRenewable);
        self::assertSame([], $model->operator);
    }
}
