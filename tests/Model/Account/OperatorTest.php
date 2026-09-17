<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\Deactivation;
use LuckyWins\YandexMusic\Model\Account\Operator;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Operator::class)]
final class OperatorTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Operator::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'productId' => 'mts.monthly',
            'phone' => '+79001234567',
            'paymentRegularity' => 'Ежемесячно',
            'title' => 'МТС',
            'suspended' => false,
            'deactivation' => [
                ['method' => 'ussd', 'instructions' => 'Send *100#'],
                ['method' => 'sms'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return [
            'productId' => 'mts.monthly',
            'phone' => '+79001234567',
            'paymentRegularity' => 'Ежемесячно',
            'title' => 'МТС',
            'suspended' => false,
        ];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Operator::class, $model);
        self::assertSame('mts.monthly', $model->productId);
        self::assertSame('МТС', $model->title);
        self::assertFalse($model->suspended);

        // Singular name, plural content — the API's choice, not ours.
        self::assertCount(2, $model->deactivation);
        self::assertInstanceOf(Deactivation::class, $model->deactivation[0]);
        self::assertSame('ussd', $model->deactivation[0]->method);
        self::assertNull($model->deactivation[1]->instructions);
    }

    protected function equalityTriple(): array
    {
        return [
            new Operator('p', '+7', 'monthly', 't', false),
            new Operator('p', '+7', 'yearly', 'other', true),
            new Operator('p', '+8', 'monthly', 't', false),
        ];
    }
}
