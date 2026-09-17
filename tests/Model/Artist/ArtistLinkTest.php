<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\ArtistLink;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistLink::class)]
final class ArtistLinkTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistLink::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'title' => 'Сайт',
            'subtitle' => 'официальный',
            'url' => 'https://example.invalid',
            'imgUrl' => 'https://avatars.invalid/link.png',
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'Сайт'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistLink::class, $model);
        self::assertSame('Сайт', $model->title);
        self::assertSame('официальный', $model->subtitle);
        self::assertSame('https://example.invalid', $model->url);
        self::assertSame('https://avatars.invalid/link.png', $model->imgUrl);
    }

    protected function equalityTriple(): array
    {
        return [new ArtistLink('Сайт', url: 'https://a.invalid'), new ArtistLink('Сайт', 'иначе', 'https://a.invalid'), new ArtistLink('Сайт', url: 'https://b.invalid')];
    }
}
