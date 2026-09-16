<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Artist\BriefInfo;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Supplement\VideoSupplement;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BriefInfo::class)]
final class BriefInfoTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return BriefInfo::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'artist' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль'],
            'albums' => [['id' => 1, 'title' => 'Hajime']],
            'alsoAlbums' => [['id' => 2, 'title' => 'Сборник']],
            'lastReleases' => [['id' => 3]],
            'lastReleaseIds' => [3],
            'popularTracks' => [['id' => 10, 'title' => 'Нирвана']],
            'similarArtists' => [['id' => 20, 'name' => 'KREC']],
            'allCovers' => [['type' => 'pic', 'uri' => 'avatars.invalid/%%']],
            'videos' => [['cover' => 'c', 'provider' => 'youtube', 'title' => 'Клип']],
            'tracksInChart' => [['position' => 100, 'progress' => 'same', 'listeners' => 5000, 'shift' => 0]],
            'stats' => ['lastMonthListeners' => 8659896, 'lastMonthListenersDelta' => -1200],
            'customWave' => ['title' => 'Moя волна'],
            'hasPromotions' => false,
            'hasTrailer' => true,
            // Domains this library has not ported; kept, not modelled.
            'playlists' => [['uid' => 1, 'kind' => 2]],
            'playlistIds' => [['uid' => 1, 'kind' => 2]],
            'concerts' => [],
            'clips' => [],
            'vinyls' => [],
            'links' => [['title' => 'Сайт', 'subtitle' => 'официальный', 'url' => 'https://a', 'imgUrl' => 'https://b']],
            'bandlinkScannerLink' => ['title' => 'Bandlink', 'url' => 'https://c'],
            'extraActions' => [],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['artist' => ['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(BriefInfo::class, $model);
        self::assertInstanceOf(Artist::class, $model->artist);
        self::assertSame('Miyagi & Эндшпиль', $model->artist->name);

        self::assertInstanceOf(Album::class, $model->albums[0] ?? null);
        self::assertInstanceOf(Album::class, $model->alsoAlbums[0] ?? null);
        self::assertInstanceOf(Track::class, $model->popularTracks[0] ?? null);
        self::assertInstanceOf(Artist::class, $model->similarArtists[0] ?? null);
        self::assertInstanceOf(Cover::class, $model->allCovers[0] ?? null);
        self::assertInstanceOf(VideoSupplement::class, $model->videos[0] ?? null);

        self::assertSame(8659896, $model->stats?->lastMonthListeners);
        self::assertSame('Moя волна', $model->customWave?->title);
        self::assertSame(100, ($model->tracksInChart[0] ?? null)?->position);
        self::assertTrue($model->hasTrailer);
        self::assertSame([3], $model->lastReleaseIds);
    }

    protected function equalityTriple(): array
    {
        return [
            new BriefInfo(new Artist(1)),
            new BriefInfo(new Artist(1), albums: []),
            new BriefInfo(new Artist(2)),
        ];
    }

    /**
     * The unported parts are kept as they arrived rather than dropped, so
     * nothing is lost while those domains wait their turn.
     */
    public function testUnportedDomainsSurviveAsRawData(): void
    {
        $model = BriefInfo::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(BriefInfo::class, $model);
        self::assertSame([['uid' => 1, 'kind' => 2]], $model->playlists);
        self::assertSame('Bandlink', $model->bandlinkScannerLink['title'] ?? null);

        // These links are not the artist's own: different shape, same name.
        $link = $model->links[0] ?? null;
        self::assertIsArray($link);
        self::assertSame('официальный', $link['subtitle'] ?? null);
    }
}
