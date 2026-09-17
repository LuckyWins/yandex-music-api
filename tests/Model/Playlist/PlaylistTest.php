<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\ActionButton;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\CoverDerivedColors;
use LuckyWins\YandexMusic\Model\CustomWave;
use LuckyWins\YandexMusic\Model\Landing\TrackId;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Model\Playlist\Brand;
use LuckyWins\YandexMusic\Model\Playlist\Contest;
use LuckyWins\YandexMusic\Model\Playlist\MadeFor;
use LuckyWins\YandexMusic\Model\Playlist\MadeForUser;
use LuckyWins\YandexMusic\Model\Playlist\OpenGraphData;
use LuckyWins\YandexMusic\Model\Playlist\PlayCounter;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistAbsence;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistAvailability;
use LuckyWins\YandexMusic\Model\Playlist\User;
use LuckyWins\YandexMusic\Model\Track\TrackShort;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use LuckyWins\YandexMusic\Tests\Support\RecordingLogger;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Playlist::class)]
final class PlaylistTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Playlist::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'uid' => 503646255,
            'kind' => 1042,
            'title' => 'Плейлист дня',
            'trackCount' => 2,
            'revision' => 4,
            'snapshot' => 1,
            'visibility' => 'private',
            'collective' => false,
            'urlPart' => 'playlist-of-the-day',
            'created' => '2019-06-01T12:00:00+00:00',
            'modified' => '2019-06-02T12:00:00+00:00',
            'available' => true,
            'isBanner' => false,
            'isPremiere' => false,
            'durationMs' => 420000,
            'likesCount' => 17,
            'description' => 'Обновляется каждый день',
            'descriptionFormatted' => 'Обновляется <b>каждый день</b>',
            'playlistUuid' => 'a9f0c1e2-0000-4000-8000-000000000001',
            'type' => 'playlistOfTheDay',
            'ready' => true,
            'everPlayed' => true,
            'generatedPlaylistType' => 'playlistOfTheDay',
            'animatedCoverUri' => 'avatars.invalid/animated/%%',
            'backgroundColor' => '#000000',
            'textColor' => '#ffffff',
            'backgroundImageUrl' => 'https://avatars.invalid/background.jpg',
            'backgroundVideoUrl' => 'https://avatars.invalid/background.mp4',
            'backgroundVideoId' => 'vid-1',
            'idForFrom' => 'playlist_of_the_day',
            'dummyDescription' => 'Пока пусто',
            'dummyPageDescription' => 'Совсем пусто',
            'metrikaId' => 987654,
            'ogImage' => 'avatars.invalid/og/%%',
            'ogTitle' => 'Плейлист дня',
            'ogDescription' => 'Каждый день новый',
            'image' => 'avatars.invalid/image/%%',
            'hasTrailer' => true,
            'coauthors' => [503646256, 503646257],
            'tags' => [['id' => 'rock', 'value' => 'Рок']],
            'prerolls' => [['id' => 'ad-1']],
            'regions' => ['RUSSIA'],
            'isForFrom' => 'whatever-shape',
            'owner' => ['uid' => 503646255, 'login' => 'andreu', 'name' => 'Андрей'],
            'cover' => ['type' => 'pic', 'uri' => 'avatars.invalid/cover/%%'],
            'coverWithoutText' => ['type' => 'pic', 'uri' => 'avatars.invalid/plain/%%'],
            'dummyCover' => ['type' => 'pic', 'uri' => 'avatars.invalid/dummy/%%'],
            'dummyRolloverCover' => ['type' => 'pic', 'uri' => 'avatars.invalid/rollover/%%'],
            'madeFor' => [
                'userInfo' => ['uid' => 503646255, 'login' => 'andreu'],
                'caseForms' => [
                    'nominative' => 'Андрей',
                    'genitive' => 'Андрея',
                    'dative' => 'Андрею',
                    'accusative' => 'Андрея',
                    'instrumental' => 'Андреем',
                    'prepositional' => 'Андрее',
                ],
            ],
            'madeForUser' => [
                'isMadeForUser' => true,
                'caseForms' => [
                    'nominative' => 'Андрей',
                    'genitive' => 'Андрея',
                    'dative' => 'Андрею',
                    'accusative' => 'Андрея',
                    'instrumental' => 'Андреем',
                    'prepositional' => 'Андрее',
                ],
            ],
            'derivedColors' => [
                'average' => '#3c3c3c',
                'waveText' => '#ffffff',
                'miniPlayer' => '#202020',
                'accent' => '#c0392b',
            ],
            'playCounter' => ['value' => 7, 'description' => '7 дней подряд', 'updated' => true],
            'playlistAbsence' => ['kind' => 1042, 'reason' => 'deleted'],
            'contest' => ['contestId' => 'newyear2019', 'status' => 'sent', 'canEdit' => false],
            'ogData' => ['title' => 'Плейлист дня', 'description' => 'Каждый день новый'],
            'branding' => [
                'image' => 'https://avatars.invalid/brand.png',
                'background' => '#000000',
                'reference' => 'https://example.invalid/promo',
                'pixels' => [],
                'theme' => 'black',
                'playlistTheme' => 'dark',
                'button' => 'Слушать',
            ],
            'customWave' => [
                'title' => 'Моя волна',
                'position' => 'bottom',
                'squareAgentAnimation' => 'https://avatars.invalid/square.json',
            ],
            'pager' => ['total' => 2, 'page' => 0, 'perPage' => 20],
            'trailer' => ['available' => true],
            'actionButton' => ['text' => 'Слушать', 'url' => 'https://example.invalid/promo', 'color' => '#ff0000'],
            'topArtist' => [['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']],
            'recentTracks' => [['id' => 31190260, 'albumId' => 4243617]],
            'tracks' => [
                ['id' => 31190260, 'albumId' => 4243617, 'timestamp' => '2019-06-01T12:00:00+00:00'],
                ['id' => 31190261, 'albumId' => 4243617, 'timestamp' => '2019-06-01T12:01:00+00:00'],
            ],
            'similarPlaylists' => [['uid' => 503646255, 'kind' => 1043, 'title' => 'Похожий']],
            'lastOwnerPlaylists' => [['uid' => 503646255, 'kind' => 1044, 'title' => 'Ещё один']],
        ];
    }

    protected static function requiredPayload(): array
    {
        // Nothing is required: the same class covers a stub and a full answer.
        return ['kind' => 1042];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Playlist::class, $model);

        self::assertSame(503646255, $model->uid);
        self::assertSame(1042, $model->kind);
        self::assertSame('Плейлист дня', $model->title);
        self::assertSame(4, $model->revision);
        self::assertSame('private', $model->visibility);
        self::assertSame(420000, $model->durationMs);
        self::assertSame('a9f0c1e2-0000-4000-8000-000000000001', $model->playlistUuid);
        self::assertSame([503646256, 503646257], $model->coauthors);

        self::assertInstanceOf(User::class, $model->owner);
        self::assertSame('andreu', $model->owner->login);
        self::assertInstanceOf(Cover::class, $model->cover);
        self::assertInstanceOf(Cover::class, $model->coverWithoutText);
        self::assertInstanceOf(Cover::class, $model->dummyCover);
        self::assertInstanceOf(Cover::class, $model->dummyRolloverCover);
        self::assertInstanceOf(MadeFor::class, $model->madeFor);
        self::assertSame('Андрею', $model->madeFor->caseForms?->dative);

        // Close to madeFor, different shape: a flag rather than the user.
        self::assertInstanceOf(MadeForUser::class, $model->madeForUser);
        self::assertTrue($model->madeForUser->isMadeForUser);
        self::assertSame('Андреем', $model->madeForUser->caseForms?->instrumental);

        self::assertInstanceOf(CoverDerivedColors::class, $model->derivedColors);
        self::assertSame('#3c3c3c', $model->derivedColors->average);
        self::assertInstanceOf(PlayCounter::class, $model->playCounter);
        self::assertInstanceOf(PlaylistAbsence::class, $model->playlistAbsence);
        self::assertInstanceOf(Contest::class, $model->contest);
        self::assertInstanceOf(OpenGraphData::class, $model->ogData);
        self::assertInstanceOf(Brand::class, $model->branding);
        self::assertInstanceOf(CustomWave::class, $model->customWave);
        self::assertSame('bottom', $model->customWave->position);
        self::assertInstanceOf(Pager::class, $model->pager);
        self::assertInstanceOf(PlaylistAvailability::class, $model->trailer);
        self::assertTrue($model->trailer->available);
        self::assertInstanceOf(ActionButton::class, $model->actionButton);
        self::assertSame('Слушать', $model->actionButton->text);

        self::assertCount(1, $model->topArtist);
        self::assertCount(1, $model->recentTracks);
        self::assertInstanceOf(TrackId::class, $model->recentTracks[0]);
        self::assertCount(2, $model->tracks);
        self::assertInstanceOf(TrackShort::class, $model->tracks[0]);
        self::assertSame('31190260:4243617', $model->tracks[0]->compositeId());
        self::assertCount(1, $model->similarPlaylists);
        self::assertSame(1043, $model->similarPlaylists[0]->kind);
        self::assertCount(1, $model->lastOwnerPlaylists);

        // Left raw on purpose: the reference does not type these either.
        self::assertSame([['id' => 'rock', 'value' => 'Рок']], $model->tags);
        self::assertSame([['id' => 'ad-1']], $model->prerolls);
        self::assertSame(['RUSSIA'], $model->regions);
        self::assertSame('whatever-shape', $model->isForFrom);
    }

    public function testOwnerKindIsThePairTheApiWants(): void
    {
        $model = Playlist::fromApi(['uid' => 503646255, 'kind' => 1042], self::client());

        self::assertInstanceOf(Playlist::class, $model);
        self::assertSame('503646255:1042', $model->ownerKind());
    }

    public function testOwnerKindNeedsBothHalves(): void
    {
        $model = Playlist::fromApi(['uid' => 503646255], self::client());

        self::assertInstanceOf(Playlist::class, $model);
        self::assertNull($model->ownerKind());
    }

    public function testCoverUrlSubstitutesTheSize(): void
    {
        $model = Playlist::fromApi([
            'kind' => 1042,
            'cover' => ['type' => 'pic', 'uri' => 'avatars.invalid/cover/%%'],
        ], self::client());

        self::assertInstanceOf(Playlist::class, $model);
        self::assertSame('https://avatars.invalid/cover/400x400', $model->coverUrl('400x400'));
    }

    public function testCoverUrlWithoutACover(): void
    {
        $model = Playlist::fromApi(['kind' => 1042], self::client());

        self::assertInstanceOf(Playlist::class, $model);
        self::assertNull($model->coverUrl());
    }

    /**
     * A playlist references playlists, so the schema is cyclic. The data is
     * not: the resolver descends only where a key is present.
     */
    public function testNestedPlaylistsTerminate(): void
    {
        $model = Playlist::fromApi([
            'kind' => 1,
            'similarPlaylists' => [[
                'kind' => 2,
                'similarPlaylists' => [['kind' => 3]],
            ]],
        ], self::client());

        self::assertInstanceOf(Playlist::class, $model);
        self::assertSame(2, $model->similarPlaylists[0]->kind);
        self::assertSame(3, $model->similarPlaylists[0]->similarPlaylists[0]->kind);
        self::assertSame([], $model->similarPlaylists[0]->similarPlaylists[0]->similarPlaylists);
    }

    /**
     * The whole payload above has to be accounted for: an unplaced field here
     * means the model is behind the API.
     */
    public function testEveryFieldOfTheFullPayloadIsKnown(): void
    {
        $logger = new RecordingLogger();

        Playlist::fromApi(self::fullPayload(), self::clientReportingUnknownFields($logger));

        self::assertSame([], $logger->unknownFields());
    }

    protected function equalityTriple(): array
    {
        return [
            new Playlist(uid: 503646255, kind: 1042),
            new Playlist(uid: 503646255, kind: 1042, title: 'другое название'),
            new Playlist(uid: 503646255, kind: 1043),
        ];
    }
}
