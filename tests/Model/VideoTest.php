<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Video;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Video::class)]
final class VideoTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Video::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'title' => 'Нирвана',
            'cover' => 'https://avatars.invalid/cover.jpg',
            'embedUrl' => 'https://www.youtube.invalid/embed/xyz',
            'provider' => 'youtube',
            'providerVideoId' => 'xyz',
            'youtubeUrl' => 'https://www.youtube.invalid/watch?v=xyz',
            'thumbnailUrl' => 'https://avatars.invalid/thumb.jpg',
            'duration' => 213,
            'text' => 'Miyagi & Эндшпиль — Нирвана',
            'htmlAutoPlayVideoPlayer' => '<iframe src="https://www.youtube.invalid/embed/xyz?autoplay=1"></iframe>',
            'regions' => ['RUSSIA', 'BELARUS'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'Нирвана'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Video::class, $model);
        self::assertSame('Нирвана', $model->title);
        self::assertSame('https://avatars.invalid/cover.jpg', $model->cover);
        self::assertSame('https://www.youtube.invalid/embed/xyz', $model->embedUrl);
        self::assertSame('youtube', $model->provider);
        self::assertSame('xyz', $model->providerVideoId);
        self::assertSame('https://www.youtube.invalid/watch?v=xyz', $model->youtubeUrl);
        self::assertSame('https://avatars.invalid/thumb.jpg', $model->thumbnailUrl);
        self::assertSame(213, $model->duration);
        self::assertSame('Miyagi & Эндшпиль — Нирвана', $model->text);
        self::assertStringContainsString('iframe', (string) $model->htmlAutoPlayVideoPlayer);
        self::assertSame(['RUSSIA', 'BELARUS'], $model->regions);
    }

    protected function equalityTriple(): array
    {
        return [
            new Video('Нирвана', provider: 'youtube', providerVideoId: 'xyz'),
            new Video('Нирвана', 'другая обложка', provider: 'youtube', providerVideoId: 'xyz'),
            new Video('Нирвана', provider: 'youtube', providerVideoId: 'abc'),
        ];
    }
}
