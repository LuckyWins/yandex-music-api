<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Search;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Model\Search\Best;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\Video;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Best::class)]
final class BestTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Best::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'type' => 'track',
            'text' => 'нирвана',
            'result' => ['id' => 31190260, 'title' => 'Нирвана'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'track'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Best::class, $model);
        self::assertSame('track', $model->type);
        self::assertSame('нирвана', $model->text);
        self::assertInstanceOf(Track::class, $model->result);
        self::assertSame('Нирвана', $model->result->title);
    }

    /**
     * What to build is decided by the response rather than by the field, so
     * every type the API can answer with has to be covered.
     */
    public function testEveryKindOfBestMatch(): void
    {
        foreach ([
            ['artist', ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль'], Artist::class],
            ['album', ['id' => 4243617, 'title' => 'Hajime'], Album::class],
            ['playlist', ['uid' => 1, 'kind' => 1042], Playlist::class],
            ['video', ['title' => 'Нирвана', 'provider' => 'youtube'], Video::class],
            ['user', ['uid' => 503646255, 'login' => 'andreu'], User::class],
            ['podcast', ['id' => 4243617, 'title' => 'Подкаст'], Album::class],
            ['podcast_episode', ['id' => 31190260, 'title' => 'Выпуск'], Track::class],
        ] as [$type, $payload, $expected]) {
            $best = Best::fromApi(['type' => $type, 'result' => $payload], self::client());

            self::assertInstanceOf(Best::class, $best, $type);
            self::assertInstanceOf($expected, $best->result, $type);
        }
    }

    /**
     * A type this library does not know leaves the result empty rather than
     * guessing at a model and throwing.
     */
    public function testAnUnknownTypeLeavesTheResultAlone(): void
    {
        $best = Best::fromApi(['type' => 'hologram', 'result' => ['id' => 1]], self::client());

        self::assertInstanceOf(Best::class, $best);
        self::assertSame('hologram', $best->type);
        self::assertNull($best->result);
    }

    public function testATypelessBestHasNoResult(): void
    {
        $best = Best::fromApi(['result' => ['id' => 31190260]], self::client());

        self::assertInstanceOf(Best::class, $best);
        self::assertNull($best->result);
    }

    protected function equalityTriple(): array
    {
        return [
            new Best('track', new Track(31190260)),
            new Best('track', new Track(31190260), 'другой запрос'),
            new Best('track', new Track(31190261)),
        ];
    }
}
