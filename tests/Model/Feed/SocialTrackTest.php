<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Feed;

use LuckyWins\YandexMusic\Model\Feed\SocialTrack;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SocialTrack::class)]
final class SocialTrackTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return SocialTrack::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'track' => ['id' => 31190260, 'title' => 'Нирвана'],
            'likedByUsers' => [
                ['uid' => 503646255, 'login' => 'andreu', 'name' => 'Андрей'],
                ['uid' => 503646256, 'login' => 'someone'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['track' => ['id' => 31190260]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(SocialTrack::class, $model);
        self::assertInstanceOf(Track::class, $model->track);
        self::assertSame('Нирвана', $model->track->title);
        self::assertCount(2, $model->likedByUsers);
        self::assertInstanceOf(User::class, $model->likedByUsers[0]);
        self::assertSame('Андрей', $model->likedByUsers[0]->name);
    }

    protected function equalityTriple(): array
    {
        return [
            new SocialTrack(new Track(31190260)),
            new SocialTrack(new Track(31190260), [new User(1, 'andreu')]),
            new SocialTrack(new Track(31190261)),
        ];
    }
}
