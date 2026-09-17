<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\ConcertTabConfig;
use LuckyWins\YandexMusic\Model\Concert\ConcertTabConfigData;
use LuckyWins\YandexMusic\Model\Concert\ConcertTabRange;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConcertTabConfig::class)]
final class ConcertTabConfigTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ConcertTabConfig::class;
    }

    protected static function fullPayload(): array
    {
        return ['config' => ['top' => ['offset' => 0, 'limit' => 5], 'feed' => ['offset' => 5, 'limit' => -1]]];
    }

    protected static function requiredPayload(): array
    {
        return ['config' => ['top' => ['offset' => 0]]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ConcertTabConfig::class, $model);
        self::assertInstanceOf(ConcertTabConfigData::class, $model->config);
        self::assertInstanceOf(ConcertTabRange::class, $model->config->top);
        self::assertSame(5, $model->config->top->limit);
        self::assertSame(-1, $model->config->feed?->limit);
    }

    protected function equalityTriple(): array
    {
        return [new ConcertTabConfig(new ConcertTabConfigData(new ConcertTabRange(0, 5))), new ConcertTabConfig(new ConcertTabConfigData(new ConcertTabRange(0, 5))), new ConcertTabConfig(new ConcertTabConfigData(new ConcertTabRange(0, 9)))];
    }
}
