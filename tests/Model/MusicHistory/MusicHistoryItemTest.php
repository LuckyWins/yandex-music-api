<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\MusicHistory;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryContextFullModel;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryItem;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryItemData;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryItemId;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MusicHistoryItem::class)]
#[CoversClass(MusicHistoryItemData::class)]
#[CoversClass(MusicHistoryContextFullModel::class)]
final class MusicHistoryItemTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MusicHistoryItem::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'type' => 'track',
            'data' => [
                'itemId' => ['trackId' => '31190260', 'albumId' => '4243617'],
                'fullModel' => ['id' => 31190260, 'title' => 'Нирвана'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'track'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MusicHistoryItem::class, $model);
        self::assertSame('track', $model->type);
        self::assertInstanceOf(MusicHistoryItemData::class, $model->data);
        self::assertInstanceOf(MusicHistoryItemId::class, $model->data->itemId);
        self::assertSame('31190260', $model->data->itemId->trackId);
        self::assertInstanceOf(Track::class, $model->track());
    }

    /**
     * Everything that is not a track is a context — an album, an artist, a
     * playlist or a station — and the same field carries all of them.
     */
    public function testEveryKindOfContext(): void
    {
        foreach ([
            ['album', ['album' => ['id' => 4243617, 'title' => 'Hajime']], 'album'],
            ['artist', ['artist' => ['id' => 4611844, 'name' => 'Miyagi']], 'artist'],
            ['playlist', ['playlist' => ['uid' => 1, 'kind' => 1042]], 'playlist'],
            ['wave', ['wave' => ['name' => 'Моя волна']], 'wave'],
        ] as [$type, $full, $filled]) {
            $item = MusicHistoryItem::fromApi([
                'type' => $type,
                'data' => ['itemId' => ['id' => '1'], 'fullModel' => $full],
            ], self::client());

            self::assertInstanceOf(MusicHistoryItem::class, $item, $type);
            self::assertNull($item->track(), $type);

            $context = $item->context();

            self::assertInstanceOf(MusicHistoryContextFullModel::class, $context, $type);
            self::assertNotNull($context->{$filled}, $type);
            self::assertSame($context->{$filled}, $context->subject(), $type);
        }
    }

    /**
     * An entry can arrive as a reference alone, without the thing itself —
     * that is what fullModelsCount limits.
     */
    public function testAnEntryWithoutItsModel(): void
    {
        $item = MusicHistoryItem::fromApi([
            'type' => 'track',
            'data' => ['itemId' => ['trackId' => '31190260', 'albumId' => '4243617']],
        ], self::client());

        self::assertInstanceOf(MusicHistoryItem::class, $item);
        self::assertSame('31190260', $item->data?->itemId?->trackId);
        self::assertNull($item->track());
        self::assertNull($item->context());
    }

    public function testAnUnknownKindIsReadAsAContext(): void
    {
        $item = MusicHistoryItem::fromApi([
            'type' => 'hologram',
            'data' => ['itemId' => ['id' => '1'], 'fullModel' => ['available' => true]],
        ], self::client());

        self::assertInstanceOf(MusicHistoryItem::class, $item);
        self::assertSame('hologram', $item->type);
        $context = $item->context();

        self::assertInstanceOf(MusicHistoryContextFullModel::class, $context);
        self::assertNull($context->subject(), 'nothing known inside it');
    }

    protected function equalityTriple(): array
    {
        return [
            new MusicHistoryItem('track', new MusicHistoryItemData(new MusicHistoryItemId('1'))),
            new MusicHistoryItem('track', new MusicHistoryItemData(new MusicHistoryItemId('1'))),
            new MusicHistoryItem('track', new MusicHistoryItemData(new MusicHistoryItemId('2'))),
        ];
    }
}
