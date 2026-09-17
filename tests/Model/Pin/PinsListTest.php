<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Pin;

use LuckyWins\YandexMusic\Model\ContentRestrictions;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pin\Pin;
use LuckyWins\YandexMusic\Model\Pin\PinData;
use LuckyWins\YandexMusic\Model\Pin\PinsList;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PinsList::class)]
#[CoversClass(Pin::class)]
#[CoversClass(PinData::class)]
final class PinsListTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PinsList::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'pins' => [
                [
                    'type' => 'album',
                    'data' => [
                        'id' => 4243617,
                        'title' => 'Hajime',
                        'cover' => ['type' => 'pic', 'uri' => 'avatars.invalid/%%'],
                        'contentRestrictions' => ['available' => true],
                    ],
                ],
                ['type' => 'artist', 'data' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']],
                ['type' => 'playlist', 'data' => ['uid' => 503646255, 'kind' => 1042, 'title' => 'Мне нравится']],
                ['type' => 'wave', 'data' => [
                    'title' => 'Моя волна',
                    'header' => 'Радио',
                    'animationUrl' => 'https://avatars.invalid/wave.json',
                    'backgroundImageUrl' => 'https://avatars.invalid/wave.jpg',
                    'stationId' => 'user:onyourwave',
                    'seeds' => ['user:onyourwave'],
                    'colors' => ['accent' => '#ff0000'],
                    'agent' => ['type' => 'artist'],
                ]],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['pins' => [['type' => 'album']]];
    }

    /**
     * One shape for four kinds: which fields are filled is what differs, and
     * the pin's own type says which to read.
     */
    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PinsList::class, $model);
        self::assertCount(4, $model->pins);

        $album = $model->pins[0];

        self::assertInstanceOf(Pin::class, $album);
        self::assertSame('album', $album->type);
        self::assertInstanceOf(PinData::class, $album->data);
        self::assertSame(4243617, $album->data->id);
        self::assertSame('Hajime', $album->data->title);
        self::assertInstanceOf(Cover::class, $album->data->cover);
        self::assertInstanceOf(ContentRestrictions::class, $album->data->contentRestrictions);

        self::assertSame('Miyagi & Эндшпиль', $model->pins[1]->data?->name);
        self::assertSame(1042, $model->pins[2]->data?->kind);

        // A pinned wave has no id: it describes itself instead.
        $wave = $model->pins[3];
        $data = $wave->data;

        self::assertSame('wave', $wave->type);
        self::assertInstanceOf(PinData::class, $data);
        self::assertNull($data->id);
        self::assertSame('user:onyourwave', $data->stationId);
        self::assertSame(['user:onyourwave'], $data->seeds);
        self::assertSame(['accent' => '#ff0000'], $data->colors);
        self::assertSame(['type' => 'artist'], $data->agent);
    }

    public function testPinsCanBeTakenByKind(): void
    {
        $model = PinsList::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(PinsList::class, $model);
        self::assertCount(1, $model->ofType('playlist'));
        self::assertSame('playlist', $model->ofType('playlist')[0]->type);
        self::assertSame([], $model->ofType('podcast'));
    }

    /**
     * What comes back is `album_item`, while the endpoint that pinned it is
     * `/pin/album`. Asking by the name you pinned with has to work.
     */
    public function testEitherSpellingOfAKindFindsIt(): void
    {
        $model = PinsList::fromApi([
            'pins' => [
                ['type' => 'album_item', 'data' => ['id' => 4243617]],
                ['type' => 'wave_item', 'data' => ['stationId' => 'user:onyourwave']],
            ],
        ], self::client());

        self::assertInstanceOf(PinsList::class, $model);
        self::assertCount(1, $model->ofType('album'));
        self::assertCount(1, $model->ofType('album_item'));
        self::assertCount(1, $model->ofType('wave'));
        self::assertSame([], $model->ofType('artist'));
    }

    protected function equalityTriple(): array
    {
        return [
            new PinsList([new Pin('album', new PinData(4243617))]),
            new PinsList([new Pin('album', new PinData(4243617))]),
            new PinsList([new Pin('album', new PinData(4243618))]),
        ];
    }
}
