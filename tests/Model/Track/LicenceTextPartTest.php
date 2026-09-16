<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\LicenceTextPart;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LicenceTextPart::class)]
final class LicenceTextPartTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return LicenceTextPart::class;
    }

    protected static function fullPayload(): array
    {
        return ['text' => 'Terms of use', 'url' => 'https://yandex.ru/legal/'];
    }

    protected static function requiredPayload(): array
    {
        return ['text' => 'Terms of use'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(LicenceTextPart::class, $model);
        self::assertSame('Terms of use', $model->text);
        self::assertSame('https://yandex.ru/legal/', $model->url);
    }

    protected function equalityTriple(): array
    {
        return [
            new LicenceTextPart('t', 'https://a'),
            new LicenceTextPart('t', 'https://b'),
            new LicenceTextPart('other'),
        ];
    }
}
