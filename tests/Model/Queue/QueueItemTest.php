<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Queue;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Queue\Context;
use LuckyWins\YandexMusic\Model\Queue\QueueItem;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(QueueItem::class)]
final class QueueItemTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return QueueItem::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'q1',
            'context' => ['type' => 'album', 'id' => '4243617', 'description' => 'Hajime'],
            'modified' => '2026-09-17T12:00:00+00:00',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'q1'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(QueueItem::class, $model);
        self::assertSame('q1', $model->id);
        self::assertInstanceOf(Context::class, $model->context);
        self::assertSame('Hajime', $model->context->description);
        self::assertSame('2026-09-17T12:00:00+00:00', $model->modified);
    }

    protected function equalityTriple(): array
    {
        return [new QueueItem('q1'), new QueueItem('q1', new Context('album')), new QueueItem('q2')];
    }
}
