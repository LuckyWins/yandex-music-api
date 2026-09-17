<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Feed;

use LuckyWins\YandexMusic\Model\Feed\AlbumEvent;
use LuckyWins\YandexMusic\Model\Feed\ArtistEvent;
use LuckyWins\YandexMusic\Model\Feed\Event;
use LuckyWins\YandexMusic\Model\Feed\SocialTrack;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Event::class)]
final class EventTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Event::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'e1',
            'type' => 'recommended-similar-artists',
            'typeForFrom' => 'feed-recommended-similar-artists',
            'title' => 'Похожие на то, что вы слушали',
            'message' => 'Вы слушали Miyagi',
            'device' => 'android',
            'tracksCount' => 12,
            'genre' => 'rap',
            'tracks' => [['id' => 31190260, 'title' => 'Нирвана']],
            'artists' => [[
                'artist' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль'],
                'tracks' => [['id' => 31190260]],
                'similarToArtistsFromHistory' => [['id' => 4611845, 'name' => 'Другой']],
                'subscribed' => false,
            ]],
            'albums' => [[
                'album' => ['id' => 4243617, 'title' => 'Hajime'],
                'tracks' => [['id' => 31190260]],
            ]],
            'socialTracks' => [[
                'track' => ['id' => 31190260, 'title' => 'Нирвана'],
                'likedByUsers' => [['uid' => 503646255, 'login' => 'andreu', 'name' => 'Андрей']],
            ]],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 'e1'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Event::class, $model);
        self::assertSame('e1', $model->id);
        self::assertSame('recommended-similar-artists', $model->type);
        self::assertSame('feed-recommended-similar-artists', $model->typeForFrom);
        self::assertSame('Похожие на то, что вы слушали', $model->title);
        self::assertSame('Вы слушали Miyagi', $model->message);
        self::assertSame('android', $model->device);
        self::assertSame(12, $model->tracksCount);
        self::assertSame('rap', $model->genre);

        self::assertCount(1, $model->tracks);
        self::assertInstanceOf(Track::class, $model->tracks[0]);

        self::assertCount(1, $model->artists);
        self::assertInstanceOf(ArtistEvent::class, $model->artists[0]);
        self::assertSame('Miyagi & Эндшпиль', $model->artists[0]->artist?->name);
        self::assertCount(1, $model->artists[0]->similarToArtistsFromHistory);
        self::assertFalse($model->artists[0]->subscribed);

        self::assertCount(1, $model->albums);
        self::assertInstanceOf(AlbumEvent::class, $model->albums[0]);
        self::assertSame('Hajime', $model->albums[0]->album?->title);

        self::assertCount(1, $model->socialTracks);
        self::assertInstanceOf(SocialTrack::class, $model->socialTracks[0]);
        self::assertSame('Андрей', $model->socialTracks[0]->likedByUsers[0]->name);
    }

    /**
     * Which list is filled depends on what the event is about, so an event
     * with none of them must still deserialize.
     */
    public function testAnEventAboutNothingInParticular(): void
    {
        $model = Event::fromApi(['id' => 'e2', 'type' => 'promotion'], self::client());

        self::assertInstanceOf(Event::class, $model);
        self::assertSame([], $model->tracks);
        self::assertSame([], $model->artists);
        self::assertSame([], $model->albums);
        self::assertSame([], $model->socialTracks);
    }

    protected function equalityTriple(): array
    {
        return [new Event('e1', 'promotion'), new Event('e1', 'promotion', title: 'иначе'), new Event('e2', 'promotion')];
    }
}
