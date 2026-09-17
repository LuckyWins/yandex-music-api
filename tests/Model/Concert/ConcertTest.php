<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\Concert;
use LuckyWins\YandexMusic\Model\Concert\ConcertCashback;
use LuckyWins\YandexMusic\Model\Concert\ConcertEventInfo;
use LuckyWins\YandexMusic\Model\Concert\ConcertMinPrice;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Concert::class)]
final class ConcertTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Concert::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'a9f0c1e2-0000-4000-8000-000000000001',
            'concertTitle' => 'Boulevard Depo',
            'city' => 'Москва',
            'place' => 'VK Stadium',
            'address' => 'Ленинградский проспект, 80',
            'datetime' => '2026-12-12T20:00:00+03:00',
            'afishaUrl' => 'https://afisha.invalid/concert/1',
            'contentRating' => '18+',
            'imageUrl' => 'https://avatars.invalid/concert.jpg',
            'images' => ['https://avatars.invalid/1.jpg', 'https://avatars.invalid/2.jpg'],
            'dataSessionId' => 'session-1',
            'cover' => ['type' => 'pic', 'uri' => 'avatars.invalid/%%'],
            'minPrice' => ['value' => 3500, 'currency' => 'RUB', 'currencySymbol' => '₽'],
            'cashback' => ['title' => 'Кешбэк баллами', 'valuePercent' => 10],
            'eventInfo' => ['type' => 'concert'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'a9f0c1e2-0000-4000-8000-000000000001'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Concert::class, $model);
        self::assertSame('a9f0c1e2-0000-4000-8000-000000000001', $model->id);
        self::assertSame('Boulevard Depo', $model->concertTitle);
        self::assertSame('Москва', $model->city);
        self::assertSame('VK Stadium', $model->place);
        self::assertSame('Ленинградский проспект, 80', $model->address);
        self::assertSame('2026-12-12T20:00:00+03:00', $model->datetime);
        self::assertSame('https://afisha.invalid/concert/1', $model->afishaUrl);
        self::assertSame('18+', $model->contentRating);
        self::assertSame('https://avatars.invalid/concert.jpg', $model->imageUrl);
        self::assertCount(2, $model->images);
        self::assertSame('session-1', $model->dataSessionId);
        self::assertInstanceOf(Cover::class, $model->cover);
        self::assertInstanceOf(ConcertMinPrice::class, $model->minPrice);
        self::assertSame(3500, $model->minPrice->value);
        self::assertInstanceOf(ConcertCashback::class, $model->cashback);
        self::assertInstanceOf(ConcertEventInfo::class, $model->eventInfo);
    }

    protected function equalityTriple(): array
    {
        return [new Concert('c1'), new Concert('c1', 'иначе'), new Concert('c2')];
    }
}
