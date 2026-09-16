<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(User::class)]
final class UserTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return User::class;
    }

    protected static function fullPayload(): array
    {
        return ['uid' => 1130000002804451, 'login' => 'user@yandex.ru', 'name' => 'Ilya', 'displayName' => 'Il`ya', 'fullName' => 'Ilya S', 'sex' => 'male', 'verified' => false, 'regions' => [225, 149]];
    }

    protected static function requiredPayload(): array
    {
        return ['uid' => 1130000002804451, 'login' => 'user@yandex.ru'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(User::class, $model);
        self::assertSame(1130000002804451, $model->uid);
        self::assertSame('user@yandex.ru', $model->login);
        self::assertSame('Ilya', $model->name);
        self::assertSame('Il`ya', $model->displayName);
        self::assertSame('male', $model->sex);
        self::assertFalse($model->verified);
        self::assertSame([225, 149], $model->regions);
    }

    protected function equalityTriple(): array
    {
        return [
            new User(1, 'a'),
            new User(1, 'a'),
            new User(2, 'a'),
        ];
    }
}
