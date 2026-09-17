<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Artist\ArtistDonationData;
use LuckyWins\YandexMusic\Model\Artist\ArtistDonationGoal;
use LuckyWins\YandexMusic\Model\Artist\ArtistDonationItem;
use LuckyWins\YandexMusic\Model\Artist\ArtistDonations;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistDonations::class)]
#[CoversClass(ArtistDonationItem::class)]
#[CoversClass(ArtistDonationData::class)]
final class ArtistDonationsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistDonations::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'donations' => [[
                'type' => 'artist-donation',
                'data' => [
                    'tipUrl' => 'https://example.invalid/tip',
                    'artist' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль'],
                    'goal' => ['title' => 'На новый альбом'],
                ],
            ]],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['donations' => [['type' => 'artist-donation']]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistDonations::class, $model);
        self::assertCount(1, $model->donations);
        self::assertInstanceOf(ArtistDonationItem::class, $model->donations[0]);
        self::assertInstanceOf(ArtistDonationData::class, $model->donations[0]->data);
        self::assertSame('https://example.invalid/tip', $model->donations[0]->data->tipUrl);
        self::assertInstanceOf(Artist::class, $model->donations[0]->data->artist);
        self::assertInstanceOf(ArtistDonationGoal::class, $model->donations[0]->data->goal);
        self::assertSame('На новый альбом', $model->donations[0]->data->goal->title);
    }

    public function testAnUnknownKindOfEntryIsSurvivable(): void
    {
        $model = ArtistDonations::fromApi([
            'donations' => [['type' => 'crowdfunding', 'data' => ['whatever' => true]]],
        ], self::client());

        self::assertInstanceOf(ArtistDonations::class, $model);
        self::assertSame('crowdfunding', $model->donations[0]->type);
        self::assertNull($model->donations[0]->data);
    }

    protected function equalityTriple(): array
    {
        return [
            new ArtistDonations([new ArtistDonationItem('artist-donation')]),
            new ArtistDonations([new ArtistDonationItem('artist-donation')]),
            new ArtistDonations([new ArtistDonationItem('other')]),
        ];
    }
}
