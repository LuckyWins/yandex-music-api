<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\Block;
use LuckyWins\YandexMusic\Model\Landing\BlockEntity;
use LuckyWins\YandexMusic\Model\Landing\PersonalPlaylistsData;
use LuckyWins\YandexMusic\Model\Landing\PlayContextsData;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistId;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Block::class)]
final class BlockTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Block::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'block-1',
            'type' => 'personal-playlists',
            'typeForFrom' => 'main-personal-playlists',
            'title' => 'Собрано для вас',
            'description' => 'Каждый день новое',
            'data' => ['isWizardPassed' => true],
            'playContext' => ['uid' => 414787002, 'kind' => 1076, 'playlistUuid' => null],
            'backgroundImageUrl' => '27701/chart-background-preview',
            'backgroundVideoUrl' => 'https://runtime.invalid/video.mp4',
            'backgroundVideoId' => 'vplvcy7wbza25cne77y7',
            'entities' => [
                ['id' => 'e1', 'type' => 'personal-playlist', 'data' => ['type' => 'playlistOfTheDay']],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'block-1'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Block::class, $model);
        self::assertSame('block-1', $model->id);
        self::assertSame('personal-playlists', $model->type);
        self::assertSame('main-personal-playlists', $model->typeForFrom);
        self::assertSame('Собрано для вас', $model->title);
        self::assertSame('Каждый день новое', $model->description);
        self::assertCount(1, $model->entities);
        self::assertInstanceOf(BlockEntity::class, $model->entities[0]);
        self::assertInstanceOf(PersonalPlaylistsData::class, $model->data);
        self::assertTrue($model->data->isWizardPassed);

        self::assertInstanceOf(PlaylistId::class, $model->playContext);
        self::assertSame('414787002:1076', $model->playContext->pair());
        self::assertSame('27701/chart-background-preview', $model->backgroundImageUrl);
        self::assertSame('https://runtime.invalid/video.mp4', $model->backgroundVideoUrl);
        self::assertSame('vplvcy7wbza25cne77y7', $model->backgroundVideoId);
    }

    public function testThePlayContextsBlockCarriesItsOwnKindOfData(): void
    {
        $block = Block::fromApi([
            'id' => 'block-2',
            'type' => 'play-contexts',
            'data' => ['otherTracks' => [['trackId' => ['id' => 31190260, 'albumId' => 4243617]]]],
        ], self::client());

        self::assertInstanceOf(Block::class, $block);
        self::assertInstanceOf(PlayContextsData::class, $block->data);
        self::assertCount(1, $block->data->otherTracks);
        self::assertSame(31190260, $block->data->otherTracks[0]->trackId?->id);
    }

    /**
     * Most blocks carry nothing beside their entities, and a block of a kind
     * this library does not know must not try.
     */
    public function testABlockWithoutItsOwnData(): void
    {
        $block = Block::fromApi(['id' => 'block-3', 'type' => 'chart', 'data' => ['whatever' => 1]], self::client());

        self::assertInstanceOf(Block::class, $block);
        self::assertNull($block->data);
    }

    protected function equalityTriple(): array
    {
        return [new Block('b1', 'chart'), new Block('b1', 'chart', title: 'иначе'), new Block('b2', 'chart')];
    }
}
