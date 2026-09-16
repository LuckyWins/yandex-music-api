<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\AutoRenewable;
use LuckyWins\YandexMusic\Model\Account\Product;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AutoRenewable::class)]
final class AutoRenewableTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return AutoRenewable::class;
    }

    protected static function fullPayload(): array
    {
        return self::requiredPayload() + [
            'product' => [
                'productId' => 'ru.yandex.mobile.music.1month',
                'type' => 'subscription',
                'duration' => 30,
                'trialDuration' => 7,
                'feature' => 'music-and-plus',
                'debug' => false,
                'plus' => true,
            ],
            'masterInfo' => ['uid' => 999, 'login' => 'owner@yandex.ru'],
            'productId' => 'ru.yandex.mobile.music.1month',
            'orderId' => 424242,
        ];
    }

    protected static function requiredPayload(): array
    {
        return [
            'expires' => '2026-10-16T12:00:00+00:00',
            'vendor' => 'AppStore',
            'vendorHelpUrl' => 'https://support.apple.com',
            'finished' => false,
        ];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(AutoRenewable::class, $model);
        self::assertSame('AppStore', $model->vendor);
        self::assertFalse($model->finished);
        self::assertSame(424242, $model->orderId);

        self::assertInstanceOf(Product::class, $model->product);
        self::assertSame(30, $model->product->duration);

        // Who pays for this seat, on a family subscription.
        self::assertInstanceOf(User::class, $model->masterInfo);
        self::assertSame('owner@yandex.ru', $model->masterInfo->login);
    }

    protected function equalityTriple(): array
    {
        return [
            new AutoRenewable('exp', 'AppStore', 'url', false),
            new AutoRenewable('exp', 'AppStore', 'url', false, orderId: 1),
            new AutoRenewable('exp', 'GooglePlay', 'url', false),
        ];
    }
}
