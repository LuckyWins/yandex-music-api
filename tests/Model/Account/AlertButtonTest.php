<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\AlertButton;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AlertButton::class)]
final class AlertButtonTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return AlertButton::class;
    }

    protected static function fullPayload(): array
    {
        return ['text' => 'Subscribe', 'bgColor' => '#ffcc00', 'textColor' => '#000000', 'uri' => 'yandexmusic://subscribe'];
    }

    protected static function requiredPayload(): array
    {
        return ['text' => 'Subscribe', 'bgColor' => '#ffcc00', 'textColor' => '#000000', 'uri' => 'yandexmusic://subscribe'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(AlertButton::class, $model);
        self::assertSame('Subscribe', $model->text);
        self::assertSame('#ffcc00', $model->bgColor);
        self::assertSame('#000000', $model->textColor);
        self::assertSame('yandexmusic://subscribe', $model->uri);
    }

    protected function equalityTriple(): array
    {
        return [
            new AlertButton('t', 'b', 'c', 'u'),
            new AlertButton('t', 'b', 'c', 'u'),
            new AlertButton('t', 'b', 'c', 'other'),
        ];
    }
}
