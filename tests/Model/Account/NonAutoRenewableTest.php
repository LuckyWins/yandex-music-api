<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\NonAutoRenewable;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(NonAutoRenewable::class)]
final class NonAutoRenewableTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return NonAutoRenewable::class;
    }

    protected static function fullPayload(): array
    {
        return ['start' => '2026-01-01T00:00:00+00:00', 'end' => '2026-02-01T00:00:00+00:00'];
    }

    protected static function requiredPayload(): array
    {
        return ['start' => '2026-01-01T00:00:00+00:00', 'end' => '2026-02-01T00:00:00+00:00'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(NonAutoRenewable::class, $model);
        self::assertSame('2026-01-01T00:00:00+00:00', $model->start);
        self::assertSame('2026-02-01T00:00:00+00:00', $model->end);
    }

    protected function equalityTriple(): array
    {
        return [
            new NonAutoRenewable('a', 'b'),
            new NonAutoRenewable('a', 'b'),
            new NonAutoRenewable('a', 'c'),
        ];
    }
}
