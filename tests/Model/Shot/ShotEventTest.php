<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Shot;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Shot\ShotEvent;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ShotEvent::class)]
final class ShotEventTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ShotEvent::class;
    }

    protected static function fullPayload(): array
    {
        return ['eventId' => 'e1', 'shots' => [['order' => 0, 'played' => false, 'shotId' => 'a', 'status' => 'ready']]];
    }

    protected static function requiredPayload(): array
    {
        return ['eventId' => 'e1'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ShotEvent::class, $model);
        self::assertSame('e1', $model->eventId);
        self::assertCount(1, $model->shots);
    }

    protected function equalityTriple(): array
    {
        return [new ShotEvent('e1'), new ShotEvent('e1'), new ShotEvent('e2')];
    }
}
