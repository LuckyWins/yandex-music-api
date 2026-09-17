<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\CaseForms;
use LuckyWins\YandexMusic\Model\Playlist\MadeFor;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MadeFor::class)]
final class MadeForTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return MadeFor::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'userInfo' => ['uid' => 503646255, 'login' => 'andreu', 'name' => 'Андрей'],
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
        return ['userInfo' => ['uid' => 503646255, 'login' => 'andreu']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(MadeFor::class, $model);
        self::assertInstanceOf(User::class, $model->userInfo);
        self::assertSame('andreu', $model->userInfo->login);
        self::assertInstanceOf(CaseForms::class, $model->caseForms);
        self::assertSame('Андрею', $model->caseForms->dative);
    }

    protected function equalityTriple(): array
    {
        return [
            new MadeFor(new User(1, 'andreu')),
            new MadeFor(new User(1, 'andreu')),
            new MadeFor(new User(2, 'someone')),
        ];
    }
}
