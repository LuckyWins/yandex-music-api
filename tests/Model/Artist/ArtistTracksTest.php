<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\ArtistTracks;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistTracks::class)]
final class ArtistTracksTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistTracks::class;
    }

    protected static function fullPayload(): array
    {
        return ['tracks' => [['id' => 1, 'title' => 'a'], ['id' => 2]], 'pager' => ['total' => 88, 'page' => 0, 'perPage' => 20]];
    }

    protected static function requiredPayload(): array
    {
        return ['tracks' => []];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistTracks::class, $model);
        self::assertCount(2, $model->tracks);
        self::assertSame('a', $model->tracks[0]->title);
        self::assertSame(88, $model->pager?->total);
    }

    protected function equalityTriple(): array
    {
        return [new ArtistTracks(), new ArtistTracks(), new ArtistTracks([], new Pager(1, 0, 1))];
    }
}
