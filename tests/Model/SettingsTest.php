<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Account\Price;
use LuckyWins\YandexMusic\Model\Account\Product;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Settings;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Settings::class)]
final class SettingsTest extends ModelTestCase
{
    private const PRODUCT = [
        'productId' => 'ru.yandex.mobile.music.1month',
        'type' => 'subscription',
        'duration' => 30,
        'trialDuration' => 7,
        'feature' => 'music-and-plus',
        'debug' => false,
        'plus' => true,
    ];

    protected static function modelClass(): string
    {
        return Settings::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'webPaymentUrl' => 'https://music.yandex.ru/pay',
            'promoCodesEnabled' => true,
            'inAppProducts' => [self::PRODUCT],
            'nativeProducts' => [self::PRODUCT],
            'webPaymentMonthProductPrice' => ['amount' => 1690, 'currency' => 'RUB'],
            'offersBatchId' => 'batch-42',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['webPaymentUrl' => 'https://music.yandex.ru/pay', 'promoCodesEnabled' => true];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Settings::class, $model);
        self::assertSame('https://music.yandex.ru/pay', $model->webPaymentUrl);
        self::assertTrue($model->promoCodesEnabled);
        self::assertCount(1, $model->inAppProducts);
        self::assertInstanceOf(Product::class, $model->inAppProducts[0]);
        self::assertCount(1, $model->nativeProducts);
        self::assertInstanceOf(Price::class, $model->webPaymentMonthProductPrice);
        self::assertSame(1690, $model->webPaymentMonthProductPrice->amount);
        self::assertSame('batch-42', $model->offersBatchId);
    }

    protected function equalityTriple(): array
    {
        return [
            new Settings('https://a', true),
            new Settings('https://a', false),
            new Settings('https://b', true),
        ];
    }
}
