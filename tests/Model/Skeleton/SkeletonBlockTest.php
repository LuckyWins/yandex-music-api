<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Skeleton;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Skeleton\SkeletonBlock;
use LuckyWins\YandexMusic\Model\Skeleton\SkeletonBlockData;
use LuckyWins\YandexMusic\Model\Skeleton\SkeletonSource;
use LuckyWins\YandexMusic\Model\Skeleton\SkeletonTab;
use LuckyWins\YandexMusic\Model\Skeleton\SkeletonViewAllAction;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SkeletonBlock::class)]
#[CoversClass(SkeletonBlockData::class)]
#[CoversClass(SkeletonTab::class)]
final class SkeletonBlockTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return SkeletonBlock::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'albums',
            'type' => 'albums',
            'data' => [
                'title' => 'Альбомы',
                'showPolicy' => 'always',
                'selectedTabIndex' => 0,
                'source' => ['uri' => '/artists/1/direct-albums', 'count' => 12, 'countWeb' => 10],
                'viewAllAction' => ['deeplink' => 'yandexmusic://artist/1/albums'],
                'tabs' => [[
                    'id' => 'albums-tab',
                    'title' => 'Альбомы',
                    'blocks' => [['id' => 'inner', 'type' => 'tracks']],
                ]],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'albums'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(SkeletonBlock::class, $model);
        self::assertSame('albums', $model->id);
        self::assertSame('albums', $model->type);

        $data = $model->data;

        self::assertInstanceOf(SkeletonBlockData::class, $data);
        self::assertSame('Альбомы', $data->title);
        self::assertSame('always', $data->showPolicy);
        self::assertSame(0, $data->selectedTabIndex);
        self::assertInstanceOf(SkeletonSource::class, $data->source);
        self::assertSame(12, $data->source->count);
        self::assertInstanceOf(SkeletonViewAllAction::class, $data->viewAllAction);
    }

    /**
     * A block holds tabs, a tab holds blocks, and the nesting has to come out
     * the other end rather than stopping at the first level.
     */
    public function testTabsHoldBlocksOfTheirOwn(): void
    {
        $model = SkeletonBlock::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(SkeletonBlock::class, $model);

        self::assertNotNull($model->data);

        $tabs = $model->data->tabs;

        self::assertCount(1, $tabs);
        self::assertInstanceOf(SkeletonTab::class, $tabs[0]);
        self::assertCount(1, $tabs[0]->blocks);
        self::assertSame('inner', $tabs[0]->blocks[0]->id);
        self::assertNull($tabs[0]->blocks[0]->data, 'the innermost block carries nothing further');
    }

    protected function equalityTriple(): array
    {
        return [
            new SkeletonBlock('albums', 'albums'),
            new SkeletonBlock('albums', 'albums', new SkeletonBlockData(title: 'Альбомы')),
            new SkeletonBlock('tracks', 'albums'),
        ];
    }
}
