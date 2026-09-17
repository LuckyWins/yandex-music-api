<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Landing\BlockEntity;
use LuckyWins\YandexMusic\Model\Landing\ChartItem;
use LuckyWins\YandexMusic\Model\Landing\MixLink;
use LuckyWins\YandexMusic\Model\Landing\PlayContext;
use LuckyWins\YandexMusic\Model\Landing\Promotion;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\GeneratedPlaylist;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BlockEntity::class)]
final class BlockEntityTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return BlockEntity::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'entity-1',
            'type' => 'album',
            'data' => ['id' => 4243617, 'title' => 'Hajime'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'entity-1'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(BlockEntity::class, $model);
        self::assertSame('entity-1', $model->id);
        self::assertSame('album', $model->type);
        self::assertInstanceOf(Album::class, $model->data);
        self::assertSame('Hajime', $model->data->title);
    }

    /**
     * Seven kinds of thing can sit in a block, and only the entity's own type
     * says which this is.
     */
    public function testEveryKindOfEntity(): void
    {
        foreach ([
            ['personal-playlist', ['type' => 'playlistOfTheDay', 'data' => ['kind' => 1042]], GeneratedPlaylist::class],
            ['promotion', ['promoId' => 'p1', 'title' => 'Промо'], Promotion::class],
            ['album', ['id' => 4243617, 'title' => 'Hajime'], Album::class],
            ['playlist', ['uid' => 1, 'kind' => 1042], Playlist::class],
            ['chart-item', ['track' => ['id' => 31190260]], ChartItem::class],
            ['play-context', ['context' => 'playlist', 'contextItem' => '1:1042'], PlayContext::class],
            ['mix-link', ['title' => 'Микс', 'url' => '/tag/rock'], MixLink::class],
        ] as [$type, $payload, $expected]) {
            $entity = BlockEntity::fromApi(['id' => 'e', 'type' => $type, 'data' => $payload], self::client());

            self::assertInstanceOf(BlockEntity::class, $entity, $type);
            self::assertInstanceOf($expected, $entity->data, $type);
        }
    }

    /**
     * A kind this library does not know keeps its type and drops its payload,
     * rather than guessing at a model and throwing.
     */
    public function testAnUnknownKindIsSurvivable(): void
    {
        $entity = BlockEntity::fromApi(
            ['id' => 'e', 'type' => 'hologram', 'data' => ['whatever' => true]],
            self::client(),
        );

        self::assertInstanceOf(BlockEntity::class, $entity);
        self::assertSame('hologram', $entity->type);
        self::assertNull($entity->data);
    }

    protected function equalityTriple(): array
    {
        return [new BlockEntity('e1', 'album'), new BlockEntity('e1', 'album'), new BlockEntity('e2', 'album')];
    }
}
