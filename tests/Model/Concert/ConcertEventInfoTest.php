<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\ConcertEventInfo;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConcertEventInfo::class)]
final class ConcertEventInfoTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ConcertEventInfo::class;
    }

    protected static function fullPayload(): array
    {
        return ['type' => 'concert'];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'concert'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ConcertEventInfo::class, $model);
        self::assertSame('concert', $model->type);
    }

    protected function equalityTriple(): array
    {
        return [new ConcertEventInfo('concert'), new ConcertEventInfo('concert'), new ConcertEventInfo('festival')];
    }
}
