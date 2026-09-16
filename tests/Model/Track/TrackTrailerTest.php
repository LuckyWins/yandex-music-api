<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\TrackTrailer;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TrackTrailer::class)]
final class TrackTrailerTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TrackTrailer::class;
    }

    protected static function fullPayload(): array
    {
        return ['title' => 'Трейлер', 'track' => ['id' => 1]];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'Трейлер'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TrackTrailer::class, $model);
        self::assertSame('Трейлер', $model->title);
        self::assertSame(1, $model->track?->id);
    }

    protected function equalityTriple(): array
    {
        return [new TrackTrailer('a'), new TrackTrailer('a'), new TrackTrailer('b')];
    }
}
