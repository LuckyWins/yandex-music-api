<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\Alert;
use LuckyWins\YandexMusic\Model\Account\AlertButton;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Alert::class)]
final class AlertTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Alert::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'alertId' => 'xxx',
            'text' => 'Оформите подписку',
            'bgColor' => '#ffcc00',
            'textColor' => '#000000',
            'alertType' => 'subscription',
            'closeButton' => true,
            'button' => [
                'text' => 'Subscribe',
                'bgColor' => '#ffffff',
                'textColor' => '#000000',
                'uri' => 'yandexmusic://subscribe',
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return [
            'alertId' => 'xxx',
            'text' => 'Оформите подписку',
            'bgColor' => '#ffcc00',
            'textColor' => '#000000',
            'alertType' => 'subscription',
        ];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Alert::class, $model);
        self::assertSame('xxx', $model->alertId);
        self::assertSame('Оформите подписку', $model->text);
        self::assertTrue($model->closeButton);
        self::assertInstanceOf(AlertButton::class, $model->button);
        self::assertSame('yandexmusic://subscribe', $model->button->uri);
    }

    protected function equalityTriple(): array
    {
        return [
            new Alert('a', 't', 'b', 'c', 'type'),
            new Alert('a', 'other', 'b', 'c', 'type'),
            new Alert('b', 't', 'b', 'c', 'type'),
        ];
    }

    public function testButtonIsNullWhenAbsent(): void
    {
        $model = Alert::fromApi(self::requiredPayload(), self::client());

        self::assertInstanceOf(Alert::class, $model);
        self::assertNull($model->button);
    }
}
