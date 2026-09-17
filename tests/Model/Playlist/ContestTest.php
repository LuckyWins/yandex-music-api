<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Contest;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Contest::class)]
final class ContestTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Contest::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'contestId' => 'newyear2019',
            'status' => 'sent',
            'canEdit' => false,
            'sent' => '2019-12-01T00:00:00+00:00',
            'withdrawn' => '2019-12-31T00:00:00+00:00',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['contestId' => 'newyear2019', 'status' => 'sent', 'canEdit' => false];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Contest::class, $model);
        self::assertSame('newyear2019', $model->contestId);
        self::assertSame('sent', $model->status);
        self::assertFalse($model->canEdit);
        self::assertSame('2019-12-01T00:00:00+00:00', $model->sent);
        self::assertSame('2019-12-31T00:00:00+00:00', $model->withdrawn);
    }

    protected function equalityTriple(): array
    {
        return [
            new Contest('newyear2019', 'sent', false),
            new Contest('newyear2019', 'sent', true),
            new Contest('newyear2019', 'withdrawn', false),
        ];
    }
}
