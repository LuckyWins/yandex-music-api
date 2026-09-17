<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\ArtistTrailerStatus;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistTrailerStatus::class)]
final class ArtistTrailerStatusTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistTrailerStatus::class;
    }

    protected static function fullPayload(): array
    {
        return ['available' => true];
    }

    protected static function requiredPayload(): array
    {
        return ['available' => true];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistTrailerStatus::class, $model);
        self::assertTrue($model->available);
    }

    protected function equalityTriple(): array
    {
        return [new ArtistTrailerStatus(true), new ArtistTrailerStatus(true), new ArtistTrailerStatus(false)];
    }
}
