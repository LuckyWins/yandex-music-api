<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Supplement;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Supplement\VideoSupplement;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(VideoSupplement::class)]
final class VideoSupplementTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return VideoSupplement::class;
    }

    protected static function fullPayload(): array
    {
        return ['cover' => 'https://example.invalid/c.jpg', 'provider' => 'youtube', 'title' => 'Клип', 'providerVideoId' => 'abc', 'url' => 'https://youtu.be/abc', 'embedUrl' => 'https://music.yandex.ru/e', 'embed' => '<iframe></iframe>'];
    }

    protected static function requiredPayload(): array
    {
        return ['cover' => 'https://example.invalid/c.jpg', 'provider' => 'youtube'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(VideoSupplement::class, $model);
        self::assertSame('youtube', $model->provider);
        self::assertSame('Клип', $model->title);
        self::assertSame('abc', $model->providerVideoId);
    }

    protected function equalityTriple(): array
    {
        return [new VideoSupplement('c', 'youtube', 't', 'v'), new VideoSupplement('c', 'youtube', 't', 'v'), new VideoSupplement('c', 'youtube', 't', 'w')];
    }
}
