<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\Concert;
use LuckyWins\YandexMusic\Model\Concert\ConcertDescription;
use LuckyWins\YandexMusic\Model\Concert\ConcertInfo;
use LuckyWins\YandexMusic\Model\Concert\ConcertMinPrice;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConcertInfo::class)]
final class ConcertInfoTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ConcertInfo::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'concert' => ['id' => 'c1', 'concertTitle' => 'Boulevard Depo', 'place' => 'VK Stadium'],
            'minPrice' => ['value' => 3500, 'currency' => 'RUB'],
            'covers' => [['type' => 'pic', 'uri' => 'avatars.invalid/%%']],
            'description' => ['text' => 'Главный экспериментатор', 'source' => 'afisha'],
            'leadArtistId' => 4611844,
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['concert' => ['id' => 'c1']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ConcertInfo::class, $model);
        self::assertInstanceOf(Concert::class, $model->concert);
        self::assertSame('VK Stadium', $model->concert->place);
        self::assertInstanceOf(ConcertMinPrice::class, $model->minPrice);
        self::assertCount(1, $model->covers);
        self::assertInstanceOf(Cover::class, $model->covers[0]);
        self::assertInstanceOf(ConcertDescription::class, $model->description);
        self::assertSame(4611844, $model->leadArtistId);
    }

    protected function equalityTriple(): array
    {
        return [
            new ConcertInfo(new Concert('c1')),
            new ConcertInfo(new Concert('c1'), new ConcertMinPrice(3500)),
            new ConcertInfo(new Concert('c2')),
        ];
    }
}
