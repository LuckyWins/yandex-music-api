<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Feed;

use LuckyWins\YandexMusic\Model\Feed\Day;
use LuckyWins\YandexMusic\Model\Feed\Event;
use LuckyWins\YandexMusic\Model\Feed\TrackWithAds;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Day::class)]
final class DayTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Day::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'day' => '2026-09-17',
            'events' => [['id' => 'e1', 'type' => 'promotion']],
            'tracksToPlay' => [['id' => 31190260, 'title' => 'Нирвана']],
            'tracksToPlayWithAds' => [
                ['type' => 'track', 'track' => ['id' => 31190260]],
                ['type' => 'ad'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['day' => '2026-09-17'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Day::class, $model);
        self::assertSame('2026-09-17', $model->day);
        self::assertCount(1, $model->events);
        self::assertInstanceOf(Event::class, $model->events[0]);
        self::assertCount(1, $model->tracksToPlay);
        self::assertInstanceOf(Track::class, $model->tracksToPlay[0]);

        // The advertisement slot carries no track, and stays an entry anyway.
        self::assertCount(2, $model->tracksToPlayWithAds);
        self::assertInstanceOf(TrackWithAds::class, $model->tracksToPlayWithAds[1]);
        self::assertSame('ad', $model->tracksToPlayWithAds[1]->type);
        self::assertNull($model->tracksToPlayWithAds[1]->track);
    }

    protected function equalityTriple(): array
    {
        return [new Day('2026-09-17'), new Day('2026-09-17', [new Event('e1')]), new Day('2026-09-16')];
    }
}
