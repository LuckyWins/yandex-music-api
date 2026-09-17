<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\Concert;
use LuckyWins\YandexMusic\Model\Concert\ConcertFeed;
use LuckyWins\YandexMusic\Model\Concert\ConcertFeedItem;
use LuckyWins\YandexMusic\Model\Concert\ConcertFeedItemData;
use LuckyWins\YandexMusic\Model\Concert\ConcertMinPrice;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConcertFeed::class)]
#[CoversClass(ConcertFeedItem::class)]
#[CoversClass(ConcertFeedItemData::class)]
final class ConcertFeedTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ConcertFeed::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'items' => [
                [
                    'type' => 'concert_item',
                    'data' => [
                        'concert' => ['id' => 'c1', 'concertTitle' => 'Boulevard Depo', 'city' => 'Москва'],
                        'minPrice' => ['value' => 3500, 'currency' => 'RUB'],
                    ],
                ],
                ['type' => 'concert_item', 'data' => ['concert' => ['id' => 'c2', 'concertTitle' => 'PHARAOH']]],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['items' => [['type' => 'concert_item']]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ConcertFeed::class, $model);
        self::assertCount(2, $model->items);
        self::assertInstanceOf(ConcertFeedItem::class, $model->items[0]);
        self::assertInstanceOf(ConcertFeedItemData::class, $model->items[0]->data);
        self::assertInstanceOf(Concert::class, $model->items[0]->data->concert);
        self::assertInstanceOf(ConcertMinPrice::class, $model->items[0]->data->minPrice);
    }

    /**
     * The service calls these `concert_item`, the way it calls a pinned album
     * `album_item`. Reading only the unsuffixed spelling is how a listing of
     * 48 concerts came back as none.
     */
    public function testBothSpellingsOfTheEntryKind(): void
    {
        $model = ConcertFeed::fromApi([
            'items' => [
                ['type' => 'concert_item', 'data' => ['concert' => ['id' => 'c1']]],
                ['type' => 'concert', 'data' => ['concert' => ['id' => 'c2']]],
            ],
        ], self::client());

        self::assertInstanceOf(ConcertFeed::class, $model);
        self::assertCount(2, $model->concerts());
    }

    public function testAnUnknownKindOfEntryIsSurvivable(): void
    {
        $model = ConcertFeed::fromApi([
            'items' => [['type' => 'festival_item', 'data' => ['whatever' => true]]],
        ], self::client());

        self::assertInstanceOf(ConcertFeed::class, $model);
        self::assertSame('festival_item', $model->items[0]->type);
        self::assertNull($model->items[0]->data);
        self::assertSame([], $model->concerts());
    }

    protected function equalityTriple(): array
    {
        return [
            new ConcertFeed([new ConcertFeedItem('concert_item')]),
            new ConcertFeed([new ConcertFeedItem('concert_item')]),
            new ConcertFeed([new ConcertFeedItem('festival_item')]),
        ];
    }
}
