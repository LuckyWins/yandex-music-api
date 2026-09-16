<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\Price;
use LuckyWins\YandexMusic\Model\Account\Product;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\LicenceTextPart;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Product::class)]
final class ProductTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Product::class;
    }

    protected static function fullPayload(): array
    {
        return self::requiredPayload() + [
            'price' => ['amount' => 1690, 'currency' => 'RUB'],
            'introPrice' => ['amount' => 1, 'currency' => 'RUB'],
            'startPrice' => ['amount' => 990, 'currency' => 'RUB'],
            'commonPeriodDuration' => 'P1M',
            'cheapest' => true,
            'title' => 'Плюс',
            'familySub' => false,
            'fbImage' => 'https://example.invalid/image.png',
            'fbName' => 'plus',
            'family' => false,
            'features' => ['no-ads', 'offline'],
            'description' => 'Подписка',
            'available' => true,
            'trialAvailable' => true,
            'licenceTextParts' => [
                ['text' => 'Условия использования', 'url' => 'https://yandex.ru/legal/'],
                ['text' => ' и оферта'],
            ],
            'paymentMethodTypes' => ['card', 'phone'],
            'offersPositionId' => 'position-3',
        ];
    }

    protected static function requiredPayload(): array
    {
        return [
            'productId' => 'ru.yandex.mobile.music.1month.autorenewable',
            'type' => 'subscription',
            'duration' => 30,
            'trialDuration' => 7,
            'feature' => 'music-and-plus',
            'debug' => false,
            'plus' => true,
        ];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Product::class, $model);
        self::assertSame('subscription', $model->type);
        self::assertSame(30, $model->duration);
        self::assertTrue($model->plus);

        // Three separate keys all deserialize into Price.
        self::assertInstanceOf(Price::class, $model->price);
        self::assertSame(1690, $model->price->amount);
        self::assertSame(1, $model->introPrice?->amount);
        self::assertSame(990, $model->startPrice?->amount);

        // The reference annotates this as Price[] but fills it with licence
        // text; the annotation is wrong and the content is what counts.
        self::assertCount(2, $model->licenceTextParts);
        self::assertInstanceOf(LicenceTextPart::class, $model->licenceTextParts[0]);
        self::assertSame('https://yandex.ru/legal/', $model->licenceTextParts[0]->url);
        self::assertNull($model->licenceTextParts[1]->url);

        self::assertSame(['no-ads', 'offline'], $model->features);
        self::assertSame(['card', 'phone'], $model->paymentMethodTypes);
        self::assertSame('position-3', $model->offersPositionId);
    }

    protected function equalityTriple(): array
    {
        $make = static fn (string $id, string $title): Product => new Product(
            $id,
            'subscription',
            30,
            7,
            'music',
            false,
            true,
            title: $title,
        );

        return [$make('a', 'x'), $make('a', 'y'), $make('b', 'x')];
    }

    /**
     * Two list-ish fields that behave differently on purpose: an absent
     * `features` is null, an absent `paymentMethodTypes` is empty. The
     * reference draws the same distinction.
     */
    public function testAbsentListsDifferFromEachOther(): void
    {
        $model = Product::fromApi(self::requiredPayload(), self::client());

        self::assertInstanceOf(Product::class, $model);
        self::assertNull($model->features);
        self::assertSame([], $model->paymentMethodTypes);
        self::assertSame([], $model->licenceTextParts);
    }
}
