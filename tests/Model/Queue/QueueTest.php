<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Queue;

use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Queue\Context;
use LuckyWins\YandexMusic\Model\Queue\Queue;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Queue::class)]
#[CoversClass(Context::class)]
final class QueueTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Queue::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'q1',
            'context' => ['type' => 'playlist', 'id' => '503646255:1042', 'description' => 'Мне нравится'],
            'tracks' => [
                ['id' => 31190260, 'albumId' => 4243617],
                ['id' => 31190261, 'albumId' => 4243617],
            ],
            'currentIndex' => 1,
            'modified' => '2026-09-17T12:00:00+00:00',
            'from' => 'mobile-playlist',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'q1'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Queue::class, $model);
        self::assertSame('q1', $model->id);
        self::assertInstanceOf(Context::class, $model->context);
        self::assertSame('playlist', $model->context->type);
        self::assertSame('503646255:1042', $model->context->id);
        self::assertCount(2, $model->tracks);
        self::assertInstanceOf(TrackId::class, $model->tracks[0]);
        self::assertSame(1, $model->currentIndex);
        self::assertSame('2026-09-17T12:00:00+00:00', $model->modified);
        self::assertSame('mobile-playlist', $model->from);
    }

    /**
     * The point of the index is to say what is playing; reading it out is
     * what a caller picking the queue up will do first.
     */
    public function testTheCurrentTrack(): void
    {
        $model = Queue::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(Queue::class, $model);
        self::assertSame(31190261, $model->current()?->id);
    }

    public function testNoIndexMeansNoCurrentTrack(): void
    {
        $model = Queue::fromApi(['id' => 'q1', 'tracks' => [['id' => 1]]], self::client());

        self::assertInstanceOf(Queue::class, $model);
        self::assertNull($model->current());
    }

    public function testAnIndexPastTheEndIsNotATrack(): void
    {
        $model = Queue::fromApi(['id' => 'q1', 'tracks' => [['id' => 1]], 'currentIndex' => 5], self::client());

        self::assertInstanceOf(Queue::class, $model);
        self::assertNull($model->current());
    }

    protected function equalityTriple(): array
    {
        return [
            new Queue(id: 'q1', modified: 'm'),
            new Queue(new Context('playlist'), id: 'q1', modified: 'm'),
            new Queue(id: 'q2', modified: 'm'),
        ];
    }
}
