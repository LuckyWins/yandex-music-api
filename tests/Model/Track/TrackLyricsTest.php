<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\TrackLyrics;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TrackLyrics::class)]
final class TrackLyricsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TrackLyrics::class;
    }

    protected static function fullPayload(): array
    {
        return ['downloadUrl' => 'https://music-lyrics.s3-private.mds.yandex.net/x', 'lyricId' => 20111802, 'externalLyricId' => '92557924', 'writers' => ['Кто-то'], 'major' => ['id' => 7, 'name' => 'lyricfind', 'prettyName' => 'LyricFind']];
    }

    protected static function requiredPayload(): array
    {
        return ['downloadUrl' => 'https://music-lyrics.s3-private.mds.yandex.net/x', 'lyricId' => 20111802, 'externalLyricId' => '92557924', 'writers' => ['Кто-то']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TrackLyrics::class, $model);
        self::assertSame(20111802, $model->lyricId);
        self::assertSame(['Кто-то'], $model->writers);
        self::assertSame('LyricFind', $model->major?->prettyName);
    }

    protected function equalityTriple(): array
    {
        return [new TrackLyrics('u', 1, 'e', []), new TrackLyrics('v', 1, 'e', []), new TrackLyrics('u', 2, 'e', [])];
    }
}
