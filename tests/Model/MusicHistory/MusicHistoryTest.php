<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\MusicHistory;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistory;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryContextFullModel;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryGroup;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryItem;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryTab;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MusicHistory::class)]
#[CoversClass(MusicHistoryTab::class)]
#[CoversClass(MusicHistoryGroup::class)]
final class MusicHistoryTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MusicHistory::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'historyTabs' => [[
                'date' => '2026-09-17',
                'items' => [[
                    'context' => [
                        'type' => 'playlist',
                        'data' => [
                            'itemId' => ['uid' => 503646255, 'kind' => 1042],
                            'fullModel' => ['playlist' => ['uid' => 503646255, 'kind' => 1042, 'title' => 'Мне нравится']],
                        ],
                    ],
                    'tracks' => [[
                        'type' => 'track',
                        'data' => [
                            'itemId' => ['trackId' => '31190260', 'albumId' => '4243617'],
                            'fullModel' => ['id' => 31190260, 'title' => 'Нирвана'],
                        ],
                    ]],
                ]],
            ]],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['historyTabs' => [['date' => '2026-09-17']]];
    }

    /**
     * A day holds stretches of listening; a stretch holds what it was played
     * from and what was played.
     */
    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MusicHistory::class, $model);
        self::assertCount(1, $model->historyTabs);

        $day = $model->historyTabs[0];

        self::assertInstanceOf(MusicHistoryTab::class, $day);
        self::assertSame('2026-09-17', $day->date);
        self::assertCount(1, $day->items);

        $group = $day->items[0];

        self::assertInstanceOf(MusicHistoryGroup::class, $group);
        self::assertInstanceOf(MusicHistoryItem::class, $group->context);
        self::assertCount(1, $group->tracks);

        // The filled-in model is a context for the source and a track for a
        // track, which only each entry's own type can say.
        $source = $group->context->context();

        self::assertInstanceOf(MusicHistoryContextFullModel::class, $source);
        self::assertSame('Мне нравится', $source->playlist?->title);
        self::assertNull($group->context->track());

        $played = $group->tracks[0]->track();

        self::assertInstanceOf(Track::class, $played);
        self::assertSame('Нирвана', $played->title);
        self::assertNull($group->tracks[0]->context());
    }

    protected function equalityTriple(): array
    {
        return [
            new MusicHistory([new MusicHistoryTab('2026-09-17')]),
            new MusicHistory([new MusicHistoryTab('2026-09-17')]),
            new MusicHistory([new MusicHistoryTab('2026-09-16')]),
        ];
    }
}
