<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\SmartPreviewParams;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SmartPreviewParams::class)]
final class SmartPreviewParamsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return SmartPreviewParams::class;
    }

    protected static function fullPayload(): array
    {
        return ['durationMs' => 30000, 'fade' => ['inStart' => 0.5, 'inStop' => 2.0]];
    }

    protected static function requiredPayload(): array
    {
        return ['durationMs' => 30000];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(SmartPreviewParams::class, $model);
        self::assertSame(30000, $model->durationMs);
        self::assertSame(0.5, $model->fade?->inStart);
    }

    protected function equalityTriple(): array
    {
        return [new SmartPreviewParams(30000), new SmartPreviewParams(30000), new SmartPreviewParams(15000)];
    }
}
