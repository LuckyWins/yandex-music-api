<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\PermissionAlerts;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PermissionAlerts::class)]
final class PermissionAlertsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PermissionAlerts::class;
    }

    protected static function fullPayload(): array
    {
        return ['alerts' => ['subscription-expired', 'payment-failed']];
    }

    protected static function requiredPayload(): array
    {
        return ['alerts' => []];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PermissionAlerts::class, $model);
        self::assertSame(['subscription-expired', 'payment-failed'], $model->alerts);
    }

    protected function equalityTriple(): array
    {
        return [
            new PermissionAlerts(['a']),
            new PermissionAlerts(['a']),
            new PermissionAlerts(['b']),
        ];
    }
}
