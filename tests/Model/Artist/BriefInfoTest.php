<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Artist\BriefInfo;
use LuckyWins\YandexMusic\Model\Artist\Vinyl;
use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistId;
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
            'concerts' => [['id' => 'concert-1']],
            'clips' => [['clipId' => 91, 'title' => 'Нирвана', 'duration' => 180]],
            'vinyls' => [['offerId' => 12345, 'title' => 'Hajime', 'year' => 2017, 'price' => 2990]],
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
     * Playlists and clips became models once their domains were ported; this
     * is the check that the wrapper actually resolves them.
     */
    public function testPortedDomainsAreTyped(): void
    {
        $model = BriefInfo::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(BriefInfo::class, $model);

        self::assertCount(1, $model->playlists);
        self::assertInstanceOf(Playlist::class, $model->playlists[0]);
        self::assertSame(2, $model->playlists[0]->kind);

        self::assertCount(1, $model->playlistIds);
        self::assertInstanceOf(PlaylistId::class, $model->playlistIds[0]);
        self::assertSame('1:2', $model->playlistIds[0]->pair());

        self::assertCount(1, $model->clips);
        self::assertInstanceOf(Clip::class, $model->clips[0]);
        self::assertSame('Нирвана', $model->clips[0]->title);

        self::assertCount(1, $model->vinyls);
        self::assertInstanceOf(Vinyl::class, $model->vinyls[0]);
        self::assertSame(2990, $model->vinyls[0]->price);
    }

    /**
     * What is left raw is left raw on purpose: those domains have not had
     * their turn, and dropping the data would lose it.
     */
    public function testUnportedDomainsSurviveAsRawData(): void
    {
        $model = BriefInfo::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(BriefInfo::class, $model);
        self::assertSame([['id' => 'concert-1']], $model->concerts);
        self::assertSame('Bandlink', $model->bandlinkScannerLink['title'] ?? null);

        // These links are not the artist's own: different shape, same name.
        $link = $model->links[0] ?? null;
        self::assertIsArray($link);
        self::assertSame('официальный', $link['subtitle'] ?? null);
    }
}
