<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Landing\PlayContext;
use LuckyWins\YandexMusic\Model\Landing\TrackShortOld;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlayContext::class)]
final class PlayContextTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PlayContext::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'client' => 'android',
            'context' => 'playlist',
            'contextItem' => '503646255:1042',
            'tracks' => [
                ['trackId' => ['id' => 31190260, 'albumId' => 4243617], 'timestamp' => '2026-09-17T12:00:00+00:00'],
            ],
            'payload' => ['uid' => 503646255, 'kind' => 3, 'title' => 'Мне нравится'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['context' => 'playlist'];
    }

    /**
     * The response calls the player `client`, which is the name the model
     * already uses for the API client it was built with. The value is kept
     * under a different name rather than lost.
     */
    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PlayContext::class, $model);
        self::assertSame('android', $model->playedIn);
        self::assertSame('playlist', $model->context);
        self::assertSame('503646255:1042', $model->contextItem);
        self::assertCount(1, $model->tracks);
        self::assertInstanceOf(TrackShortOld::class, $model->tracks[0]);
        self::assertSame(31190260, $model->tracks[0]->trackId?->id);
        self::assertInstanceOf(Playlist::class, $model->payload);
        self::assertSame('Мне нравится', $model->payload->title);
    }

    /**
     * A context can be about an album or an artist just as easily, and only
     * its own `context` says which — reading every payload as a playlist is
     * how an album ends up being poured into one.
     */
    public function testThePayloadFollowsTheContext(): void
    {
        $album = PlayContext::fromApi([
            'context' => 'album',
            'contextItem' => '4243617',
            'payload' => ['id' => 4243617, 'title' => 'Hajime'],
        ], self::client());

        self::assertInstanceOf(PlayContext::class, $album);
        self::assertInstanceOf(Album::class, $album->payload);
        self::assertSame('Hajime', $album->payload->title);

        $artist = PlayContext::fromApi([
            'context' => 'artist',
            'contextItem' => '4611844',
            'payload' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль'],
        ], self::client());

        self::assertInstanceOf(PlayContext::class, $artist);
        self::assertInstanceOf(Artist::class, $artist->payload);
        self::assertSame('Miyagi & Эндшпиль', $artist->payload->name);
    }

    public function testAnUnknownContextLeavesThePayloadAlone(): void
    {
        $model = PlayContext::fromApi([
            'context' => 'radio',
            'payload' => ['whatever' => true],
        ], self::client());

        self::assertInstanceOf(PlayContext::class, $model);
        self::assertNull($model->payload);
    }

    /**
     * The back-reference must still be the client, not the string from the
     * response.
     */
    public function testTheBackReferenceSurvivesTheNameClash(): void
    {
        $client = self::client();
        $model = PlayContext::fromApi(self::fullPayload(), $client);

        self::assertInstanceOf(PlayContext::class, $model);
        self::assertSame($client, $model->client);
    }

    protected function equalityTriple(): array
    {
        return [
            new PlayContext('android', 'playlist', '1:1042'),
            new PlayContext('ios', 'playlist', '1:1042'),
            new PlayContext('android', 'playlist', '1:1043'),
        ];
    }
}
