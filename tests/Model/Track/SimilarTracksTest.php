<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\SimilarTracks;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SimilarTracks::class)]
final class SimilarTracksTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return SimilarTracks::class;
    }

    protected static function fullPayload(): array
    {
        return ['track' => ['id' => 1], 'similarTracks' => [['id' => 2], ['id' => 3]]];
    }

    protected static function requiredPayload(): array
    {
        return ['track' => ['id' => 1]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(SimilarTracks::class, $model);
        self::assertSame(1, $model->track?->id);
        self::assertCount(2, $model->similarTracks);
        self::assertSame(3, $model->similarTracks[1]->id);
    }

    protected function equalityTriple(): array
    {
        return [new SimilarTracks(), new SimilarTracks(), new SimilarTracks(new Track(1))];
    }
}
