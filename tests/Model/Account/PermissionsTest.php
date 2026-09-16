<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\Permissions;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Permissions::class)]
final class PermissionsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Permissions::class;
    }

    protected static function fullPayload(): array
    {
        return ['until' => '2026-12-31T00:00:00+00:00', 'values' => ['feed-play', 'full'], 'default' => ['landing-play']];
    }

    protected static function requiredPayload(): array
    {
        return ['until' => '2026-12-31T00:00:00+00:00', 'values' => ['feed-play', 'full'], 'default' => ['landing-play']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Permissions::class, $model);
        self::assertSame('2026-12-31T00:00:00+00:00', $model->until);
        self::assertSame(['feed-play', 'full'], $model->values);
        self::assertSame(['landing-play'], $model->default);
    }

    protected function equalityTriple(): array
    {
        return [
            new Permissions('u', ['a'], ['b']),
            new Permissions('u', ['a'], ['b']),
            new Permissions('u', ['a'], ['c']),
        ];
    }
}
