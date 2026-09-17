<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Concert;

use LuckyWins\YandexMusic\Model\Concert\ArtistConcerts;
use LuckyWins\YandexMusic\Model\Concert\Concert;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ArtistConcerts::class)]
final class ArtistConcertsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ArtistConcerts::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'artistTitle' => 'Miyagi & Эндшпиль',
            'concerts' => [
                ['id' => 'c1', 'city' => 'Москва', 'datetime' => '2026-12-12T20:00:00+03:00'],
                ['id' => 'c2', 'city' => 'Санкт-Петербург'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['artistTitle' => 'Miyagi & Эндшпиль'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ArtistConcerts::class, $model);
        self::assertSame('Miyagi & Эндшпиль', $model->artistTitle);
        self::assertCount(2, $model->concerts);
        self::assertInstanceOf(Concert::class, $model->concerts[0]);
        self::assertSame('Москва', $model->concerts[0]->city);
    }

    /**
     * An artist with nothing coming up still answers, with a title and an
     * empty list — checked against the live API.
     */
    public function testAnArtistWithNoDates(): void
    {
        $model = ArtistConcerts::fromApi(['artistTitle' => 'Miyagi & Эндшпиль', 'concerts' => []], self::client());

        self::assertInstanceOf(ArtistConcerts::class, $model);
        self::assertSame([], $model->concerts);
    }

    protected function equalityTriple(): array
    {
        return [
            new ArtistConcerts('Miyagi'),
            new ArtistConcerts('Miyagi'),
            new ArtistConcerts('Другой'),
        ];
    }
}
