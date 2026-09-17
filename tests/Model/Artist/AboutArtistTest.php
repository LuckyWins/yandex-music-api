<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\AboutArtist;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Artist\ArtistLink;
use LuckyWins\YandexMusic\Model\Artist\Stats;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AboutArtist::class)]
final class AboutArtistTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return AboutArtist::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'artist' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль'],
            'stats' => ['lastMonthListeners' => 8659896, 'lastMonthListenersDelta' => 1200],
            'description' => 'Дуэт из Владикавказа',
            'artistType' => 'artist',
            'links' => [['title' => 'Сайт', 'url' => 'https://example.invalid']],
            'covers' => [['type' => 'pic', 'uri' => 'avatars.invalid/%%']],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['description' => 'Дуэт из Владикавказа'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(AboutArtist::class, $model);
        self::assertInstanceOf(Artist::class, $model->artist);
        self::assertInstanceOf(Stats::class, $model->stats);
        self::assertSame('Дуэт из Владикавказа', $model->description);
        self::assertSame('artist', $model->artistType);
        self::assertCount(1, $model->links);
        self::assertInstanceOf(ArtistLink::class, $model->links[0]);
        self::assertCount(1, $model->covers);
        self::assertInstanceOf(Cover::class, $model->covers[0]);
    }

    protected function equalityTriple(): array
    {
        return [new AboutArtist(new Artist(4611844, 'Miyagi')), new AboutArtist(new Artist(4611844, 'Miyagi'), description: 'иначе'), new AboutArtist(new Artist(4611845, 'Другой'))];
    }
}
