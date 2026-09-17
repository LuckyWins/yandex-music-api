<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\Account;
use LuckyWins\YandexMusic\Model\Account\PassportPhone;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Account::class)]
final class AccountTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Account::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'now' => '2026-09-16T12:00:00+00:00',
            'serviceAvailable' => true,
            'region' => 149,
            'uid' => 1130000002804451,
            'login' => 'user@yandex.ru',
            'fullName' => 'Илья Семёнов',
            'secondName' => 'Семёнов',
            'firstName' => 'Илья',
            'displayName' => 'Ilya',
            'hostedUser' => false,
            'birthday' => '1999-08-10',
            'passportPhones' => [['phone' => '+79001234567']],
            'registeredAt' => '2018-06-10T09:34:22+00:00',
            'hasInfoForAppMetrica' => false,
            'child' => false,
            'regionCode' => 'RU',
            'nonOwnerFamilyMember' => false,
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['now' => '2026-09-16T12:00:00+00:00', 'serviceAvailable' => true];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Account::class, $model);
        self::assertSame('2026-09-16T12:00:00+00:00', $model->now);
        self::assertTrue($model->serviceAvailable);
        self::assertSame(149, $model->region);
        self::assertSame(1130000002804451, $model->uid);
        self::assertSame('user@yandex.ru', $model->login);
        self::assertSame('Илья Семёнов', $model->fullName);
        self::assertSame('1999-08-10', $model->birthday);
        self::assertFalse($model->child);
        self::assertSame('RU', $model->regionCode);
        self::assertFalse($model->nonOwnerFamilyMember);

        self::assertCount(1, $model->passportPhones);
        self::assertInstanceOf(PassportPhone::class, $model->passportPhones[0]);
        self::assertSame('+79001234567', $model->passportPhones[0]->phone);
    }

    protected function equalityTriple(): array
    {
        return [
            new Account('now', true, uid: 1),
            new Account('other', false, uid: 1),
            new Account('now', true, uid: 2),
        ];
    }

    /**
     * A phone list that never arrived is empty, not null — list fields do not
     * carry the absent/empty distinction.
     */
    public function testPhonesDefaultToAnEmptyList(): void
    {
        $model = Account::fromApi(self::requiredPayload(), self::client());

        self::assertInstanceOf(Account::class, $model);
        self::assertSame([], $model->passportPhones);
    }

    /**
     * Without a uid there is nothing to key on, so two anonymous accounts are
     * only the same when they are the same object.
     */
    public function testAnonymousAccountsAreNotInterchangeable(): void
    {
        $a = new Account('now', true);
        $b = new Account('now', true);

        self::assertFalse($a->equals($b));
        self::assertTrue($a->equals($a));
    }
}
