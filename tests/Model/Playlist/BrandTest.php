<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Brand;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Brand::class)]
final class BrandTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Brand::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'image' => 'https://avatars.invalid/brand.png',
            'background' => '#000000',
            'reference' => 'https://example.invalid/promo',
            'pixels' => ['https://example.invalid/pixel/1', 'https://example.invalid/pixel/2'],
            'theme' => 'black',
            'playlistTheme' => 'dark',
            'button' => 'Слушать',
        ];
    }

    protected static function requiredPayload(): array
    {
        return self::fullPayload();
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Brand::class, $model);
        self::assertSame('https://avatars.invalid/brand.png', $model->image);
        self::assertSame('#000000', $model->background);
        self::assertSame('https://example.invalid/promo', $model->reference);
        self::assertCount(2, $model->pixels);
        self::assertSame('black', $model->theme);
        self::assertSame('dark', $model->playlistTheme);
        self::assertSame('Слушать', $model->button);
    }

    protected function equalityTriple(): array
    {
        $brand = static fn (string $reference): Brand => new Brand(
            'https://avatars.invalid/brand.png',
            '#000000',
            $reference,
            [],
            'black',
            'dark',
            'Слушать',
        );

        return [$brand('https://a.invalid'), $brand('https://a.invalid'), $brand('https://b.invalid')];
    }
}
