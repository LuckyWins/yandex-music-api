<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Feed;

use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Feed\ArtistEvent;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistEvent::class)]
final class ArtistEventTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistEvent::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'artist' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль'],
            'tracks' => [['id' => 31190260, 'title' => 'Нирвана']],
            'similarToArtistsFromHistory' => [['id' => 4611845, 'name' => 'Другой']],
            'subscribed' => true,
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['artist' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistEvent::class, $model);
        self::assertInstanceOf(Artist::class, $model->artist);
        self::assertSame('Miyagi & Эндшпиль', $model->artist->name);
        self::assertCount(1, $model->tracks);
        self::assertInstanceOf(Track::class, $model->tracks[0]);
        self::assertCount(1, $model->similarToArtistsFromHistory);
        self::assertSame('Другой', $model->similarToArtistsFromHistory[0]->name);
        self::assertTrue($model->subscribed);
    }

    protected function equalityTriple(): array
    {
        return [
            new ArtistEvent(new Artist(4611844, 'Miyagi')),
            new ArtistEvent(new Artist(4611844, 'Miyagi'), subscribed: true),
            new ArtistEvent(new Artist(4611845, 'Другой')),
        ];
    }
}
