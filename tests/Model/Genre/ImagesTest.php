<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Genre;

use LuckyWins\YandexMusic\Model\Genre\Images;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Images::class)]
final class ImagesTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Images::class;
    }

    protected static function fullPayload(): array
    {
        return [
            '20x20' => 'https://avatars.invalid/20.jpg',
            '208x208' => 'https://avatars.invalid/208.jpg',
            '300x300' => 'https://avatars.invalid/300.jpg',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['208x208' => 'https://avatars.invalid/208.jpg'];
    }

    /**
     * The response keys begin with a digit and the properties cannot, so they
     * differ by a leading underscore — which the key matching ignores.
     */
    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Images::class, $model);
        self::assertSame('https://avatars.invalid/20.jpg', $model->_20x20);
        self::assertSame('https://avatars.invalid/208.jpg', $model->_208x208);
        self::assertSame('https://avatars.invalid/300.jpg', $model->_300x300);
    }

    protected function equalityTriple(): array
    {
        return [new Images(_208x208: 'a'), new Images(_208x208: 'a'), new Images(_208x208: 'b')];
    }
}
