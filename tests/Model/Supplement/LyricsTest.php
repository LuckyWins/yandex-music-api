<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Supplement;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Supplement\Lyrics;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Lyrics::class)]
final class LyricsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Lyrics::class;
    }

    protected static function fullPayload(): array
    {
        return ['id' => 1, 'lyrics' => 'Первые строки', 'fullLyrics' => 'Весь текст', 'hasRights' => true, 'showTranslation' => false, 'textLanguage' => 'ru', 'url' => 'https://genius.com/x'];
    }

    protected static function requiredPayload(): array
    {
        return ['id' => 1, 'lyrics' => 'Первые строки', 'fullLyrics' => 'Весь текст', 'hasRights' => true, 'showTranslation' => false];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Lyrics::class, $model);
        self::assertSame('Весь текст', $model->fullLyrics);
        self::assertTrue($model->hasRights);
        self::assertSame('ru', $model->textLanguage);
    }

    protected function equalityTriple(): array
    {
        return [new Lyrics(1, 'a', 'b', true, false), new Lyrics(1, 'a', 'b', true, false), new Lyrics(2, 'a', 'b', true, false)];
    }
}
