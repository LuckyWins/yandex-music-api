<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Feed;

use LuckyWins\YandexMusic\Model\Feed\TrackWithAds;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TrackWithAds::class)]
final class TrackWithAdsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TrackWithAds::class;
    }

    protected static function fullPayload(): array
    {
        return ['type' => 'track', 'track' => ['id' => 31190260, 'title' => 'Нирвана']];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'track'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TrackWithAds::class, $model);
        self::assertSame('track', $model->type);
        self::assertInstanceOf(Track::class, $model->track);
        self::assertSame('Нирвана', $model->track->title);
    }

    protected function equalityTriple(): array
    {
        return [
            new TrackWithAds('track', new Track(1)),
            new TrackWithAds('track', new Track(1)),
            new TrackWithAds('track', new Track(2)),
        ];
    }
}
