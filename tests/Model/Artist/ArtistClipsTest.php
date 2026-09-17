<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\ArtistClipData;
use LuckyWins\YandexMusic\Model\Artist\ArtistClipItem;
use LuckyWins\YandexMusic\Model\Artist\ArtistClips;
use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistClips::class)]
#[CoversClass(ArtistClipItem::class)]
#[CoversClass(ArtistClipData::class)]
final class ArtistClipsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistClips::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'pager' => ['total' => 2, 'page' => 0, 'perPage' => 20],
            'items' => [
                [
                    'type' => 'clip',
                    'data' => [
                        'clip' => ['clipId' => 91, 'title' => 'Нирвана'],
                        'artists' => [['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']],
                    ],
                ],
                ['type' => 'clip', 'data' => ['clip' => ['clipId' => 92]]],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['items' => [['type' => 'clip']]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistClips::class, $model);
        self::assertInstanceOf(Pager::class, $model->pager);
        self::assertCount(2, $model->items);
        self::assertInstanceOf(ArtistClipItem::class, $model->items[0]);
        self::assertInstanceOf(ArtistClipData::class, $model->items[0]->data);
        self::assertInstanceOf(Clip::class, $model->items[0]->data->clip);
        self::assertCount(1, $model->items[0]->data->artists);
    }

    /**
     * The wrapping is what the API sends; a caller wants the clips.
     */
    public function testTheClipsComeOutOfTheWrapping(): void
    {
        $model = ArtistClips::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(ArtistClips::class, $model);

        $clips = $model->clips();

        self::assertCount(2, $clips);
        self::assertSame('Нирвана', $clips[0]->title);
    }

    /**
     * An entry of a kind this library does not know keeps its type and drops
     * its payload, as everywhere else that a type decides the shape.
     */
    public function testAnUnknownKindOfEntryIsSurvivable(): void
    {
        $model = ArtistClips::fromApi([
            'items' => [['type' => 'hologram', 'data' => ['whatever' => true]]],
        ], self::client());

        self::assertInstanceOf(ArtistClips::class, $model);
        self::assertSame('hologram', $model->items[0]->type);
        self::assertNull($model->items[0]->data);
        self::assertSame([], $model->clips());
    }

    protected function equalityTriple(): array
    {
        return [
            new ArtistClips([new ArtistClipItem('clip')]),
            new ArtistClips([new ArtistClipItem('clip')]),
            new ArtistClips([new ArtistClipItem('other')]),
        ];
    }
}
