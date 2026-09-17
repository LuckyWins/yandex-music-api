<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\ArtistLink;
use LuckyWins\YandexMusic\Model\Artist\ArtistLinks;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistLinks::class)]
final class ArtistLinksTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistLinks::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'links' => [
                ['title' => 'Сайт', 'url' => 'https://example.invalid'],
                ['title' => 'Telegram', 'url' => 'https://t.invalid'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['links' => [['title' => 'Сайт']]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistLinks::class, $model);
        self::assertCount(2, $model->links);
        self::assertInstanceOf(ArtistLink::class, $model->links[0]);
        self::assertSame('Telegram', $model->links[1]->title);
    }

    protected function equalityTriple(): array
    {
        return [new ArtistLinks([new ArtistLink('Сайт')]), new ArtistLinks([new ArtistLink('Сайт')]), new ArtistLinks([new ArtistLink('Telegram')])];
    }
}
