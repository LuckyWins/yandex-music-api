<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\ContentRestrictions;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ContentRestrictions::class)]
final class ContentRestrictionsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ContentRestrictions::class;
    }

    protected static function fullPayload(): array
    {
        return ['available' => false, 'disclaimers' => ['modal']];
    }

    /**
     * Every field here is optional, so there is no smaller payload than one
     * arbitrary field — an empty one deserializes to null by design.
     */
    protected static function requiredPayload(): array
    {
        return ['available' => false];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ContentRestrictions::class, $model);
        self::assertFalse($model->available);
        self::assertSame(['modal'], $model->disclaimers);
    }

    protected function equalityTriple(): array
    {
        return [new ContentRestrictions(false), new ContentRestrictions(false), new ContentRestrictions(true)];
    }
}
