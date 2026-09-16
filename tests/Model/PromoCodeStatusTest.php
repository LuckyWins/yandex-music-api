<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Account\Status;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\PromoCodeStatus;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PromoCodeStatus::class)]
final class PromoCodeStatusTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return PromoCodeStatus::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'status' => 'code-not-found',
            'statusDesc' => 'Промокод не найден',
            'accountStatus' => [
                'account' => ['now' => '2026-09-16T12:00:00+00:00', 'serviceAvailable' => true, 'uid' => 1],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['status' => 'code-not-found', 'statusDesc' => 'Промокод не найден'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(PromoCodeStatus::class, $model);
        self::assertSame('code-not-found', $model->status);
        self::assertSame('Промокод не найден', $model->statusDesc);

        // Redeeming a code hands back the updated account alongside the verdict.
        self::assertInstanceOf(Status::class, $model->accountStatus);
        self::assertSame(1, $model->accountStatus->account?->uid);
    }

    protected function equalityTriple(): array
    {
        return [
            new PromoCodeStatus('ok', 'Готово'),
            new PromoCodeStatus('ok', 'Готово'),
            new PromoCodeStatus('failed', 'Готово'),
        ];
    }
}
