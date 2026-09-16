<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Album;

use LuckyWins\YandexMusic\Model\Album\AlbumActionButton;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AlbumActionButton::class)]
final class AlbumActionButtonTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return AlbumActionButton::class;
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
        self::assertInstanceOf(AlbumActionButton::class, $model);
        self::assertSame('Слушать', $model->text);
        self::assertSame('#ffcc00', $model->color);
    }

    protected function equalityTriple(): array
    {
        return [new AlbumActionButton('a'), new AlbumActionButton('a'), new AlbumActionButton('b')];
    }
}
