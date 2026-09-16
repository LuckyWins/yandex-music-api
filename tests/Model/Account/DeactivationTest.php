<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\Deactivation;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Deactivation::class)]
final class DeactivationTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Deactivation::class;
    }

    protected static function fullPayload(): array
    {
        return ['method' => 'ussd', 'instructions' => 'Send *100# and follow the prompts'];
    }

    protected static function requiredPayload(): array
    {
        return ['method' => 'ussd'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Deactivation::class, $model);
        self::assertSame('ussd', $model->method);
        self::assertSame('Send *100# and follow the prompts', $model->instructions);
    }

    protected function equalityTriple(): array
    {
        return [
            new Deactivation('ussd', 'x'),
            new Deactivation('ussd', 'x'),
            new Deactivation('ussd', 'y'),
        ];
    }
}
