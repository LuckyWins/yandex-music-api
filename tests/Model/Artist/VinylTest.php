<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\Vinyl;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Vinyl::class)]
final class VinylTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Vinyl::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'url' => 'https://market.invalid/offer/1',
            'title' => 'Hajime',
            'year' => 2017,
            'price' => 2990,
            'media' => 'LP',
            'offerId' => 12345,
            'artistIds' => [4611844],
            'picture' => 'avatars.invalid/vinyl/%%',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['offerId' => 12345];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Vinyl::class, $model);
        self::assertSame('https://market.invalid/offer/1', $model->url);
        self::assertSame('Hajime', $model->title);
        self::assertSame(2017, $model->year);
        self::assertSame(2990, $model->price);
        self::assertSame('LP', $model->media);
        self::assertSame(12345, $model->offerId);
        self::assertSame([4611844], $model->artistIds);
        self::assertSame('avatars.invalid/vinyl/%%', $model->picture);
    }

    protected function equalityTriple(): array
    {
        return [
            new Vinyl('https://a.invalid', offerId: 1),
            new Vinyl('https://a.invalid', 'иначе', offerId: 1),
            new Vinyl('https://b.invalid', offerId: 2),
        ];
    }
}
