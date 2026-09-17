<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\CaseForms;
use LuckyWins\YandexMusic\Model\Playlist\MadeForUser;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MadeForUser::class)]
final class MadeForUserTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MadeForUser::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'isMadeForUser' => true,
            'caseForms' => [
                'nominative' => 'Андрей',
                'genitive' => 'Андрея',
                'dative' => 'Андрею',
                'accusative' => 'Андрея',
                'instrumental' => 'Андреем',
                'prepositional' => 'Андрее',
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['isMadeForUser' => true];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MadeForUser::class, $model);
        self::assertTrue($model->isMadeForUser);
        self::assertInstanceOf(CaseForms::class, $model->caseForms);
        self::assertSame('Андрею', $model->caseForms->dative);
    }

    protected function equalityTriple(): array
    {
        return [new MadeForUser(true), new MadeForUser(true), new MadeForUser(false)];
    }
}
