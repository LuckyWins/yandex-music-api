<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\ActionButton;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ActionButton::class)]
final class ActionButtonTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ActionButton::class;
    }

    protected static function fullPayload(): array
    {
        return ['text' => 'Слушать', 'url' => 'yandexmusic://album/1', 'color' => '#ffcc00'];
    }

    /**
     * Every field here is optional, so there is no smaller payload than one
     * arbitrary field — an empty one deserializes to null by design.
     */
    protected static function requiredPayload(): array
    {
        return ['text' => 'Слушать'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ActionButton::class, $model);
        self::assertSame('Слушать', $model->text);
        self::assertSame('#ffcc00', $model->color);
    }

    protected function equalityTriple(): array
    {
        return [new ActionButton('a'), new ActionButton('a'), new ActionButton('b')];
    }
}
