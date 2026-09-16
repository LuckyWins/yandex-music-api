<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\LyricsInfo;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LyricsInfo::class)]
final class LyricsInfoTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return LyricsInfo::class;
    }

    protected static function fullPayload(): array
    {
        return ['hasAvailableSyncLyrics' => true, 'hasAvailableTextLyrics' => true];
    }

    protected static function requiredPayload(): array
    {
        return ['hasAvailableSyncLyrics' => true, 'hasAvailableTextLyrics' => true];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(LyricsInfo::class, $model);
        self::assertTrue($model->hasAvailableSyncLyrics);
        self::assertTrue($model->hasAvailableTextLyrics);
    }

    protected function equalityTriple(): array
    {
        return [new LyricsInfo(true, true), new LyricsInfo(true, true), new LyricsInfo(false, true)];
    }
}
