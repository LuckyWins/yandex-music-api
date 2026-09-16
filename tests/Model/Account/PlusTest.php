<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\Plus;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Plus::class)]
final class PlusTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Plus::class;
    }

    protected static function fullPayload(): array
    {
        return ['hasPlus' => true, 'isTutorialCompleted' => false];
    }

    protected static function requiredPayload(): array
    {
        return ['hasPlus' => true, 'isTutorialCompleted' => false];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Plus::class, $model);
        self::assertTrue($model->hasPlus);
        self::assertFalse($model->isTutorialCompleted);
    }

    protected function equalityTriple(): array
    {
        return [
            new Plus(true, false),
            new Plus(true, false),
            new Plus(false, false),
        ];
    }
}
