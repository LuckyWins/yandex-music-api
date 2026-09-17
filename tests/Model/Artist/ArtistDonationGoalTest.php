<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Artist;

use LuckyWins\YandexMusic\Model\Artist\ArtistDonationGoal;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistDonationGoal::class)]
final class ArtistDonationGoalTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistDonationGoal::class;
    }

    protected static function fullPayload(): array
    {
        return ['title' => 'На новый альбом'];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'На новый альбом'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistDonationGoal::class, $model);
        self::assertSame('На новый альбом', $model->title);
    }

    protected function equalityTriple(): array
    {
        return [new ArtistDonationGoal('На альбом'), new ArtistDonationGoal('На альбом'), new ArtistDonationGoal('На тур')];
    }
}
