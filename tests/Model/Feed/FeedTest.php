<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Feed;

use LuckyWins\YandexMusic\Model\Feed\Day;
use LuckyWins\YandexMusic\Model\Feed\Feed;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\GeneratedPlaylist;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Feed::class)]
final class FeedTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Feed::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'canGetMoreEvents' => true,
            'pumpkin' => false,
            'isWizardPassed' => true,
            'today' => '2026-09-17',
            'nextRevision' => '2026-09-16',
            'headlines' => ['Новое для вас'],
            'generatedPlaylists' => [
                ['type' => 'playlistOfTheDay', 'ready' => true, 'data' => ['uid' => 1, 'kind' => 1042]],
            ],
            'days' => [
                [
                    'day' => '2026-09-17',
                    'events' => [['id' => 'e1', 'type' => 'recommended-similar-artists', 'title' => 'Похожие']],
                    'tracksToPlay' => [['id' => 31190260, 'title' => 'Нирвана']],
                    'tracksToPlayWithAds' => [['type' => 'track', 'track' => ['id' => 31190260]]],
                ],
                ['day' => '2026-09-16', 'events' => []],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['today' => '2026-09-17'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Feed::class, $model);
        self::assertTrue($model->canGetMoreEvents);
        self::assertFalse($model->pumpkin);
        self::assertTrue($model->isWizardPassed);
        self::assertSame('2026-09-17', $model->today);
        self::assertSame('2026-09-16', $model->nextRevision);
        self::assertSame(['Новое для вас'], $model->headlines);

        self::assertCount(1, $model->generatedPlaylists);
        self::assertInstanceOf(GeneratedPlaylist::class, $model->generatedPlaylists[0]);
        self::assertSame(1042, $model->generatedPlaylists[0]->data?->kind);

        self::assertCount(2, $model->days);
        self::assertInstanceOf(Day::class, $model->days[0]);
        self::assertCount(1, $model->days[0]->events);
        self::assertCount(1, $model->days[0]->tracksToPlay);
        self::assertCount(1, $model->days[0]->tracksToPlayWithAds);
    }

    protected function equalityTriple(): array
    {
        return [new Feed(today: '2026-09-17'), new Feed(true, today: '2026-09-17'), new Feed(today: '2026-09-16')];
    }
}
