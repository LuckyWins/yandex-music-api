<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Rotor;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Rotor\Id;
use LuckyWins\YandexMusic\Model\Rotor\Sequence;
use LuckyWins\YandexMusic\Model\Rotor\StationTracksResult;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(StationTracksResult::class)]
final class StationTracksResultTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return StationTracksResult::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => ['type' => 'genre', 'tag' => 'allrock'],
            'batchId' => '1789647430947178-11328995973787230000.QxPI',
            'radioSessionId' => 'aBcD-1234',
            'pumpkin' => false,
            'sequence' => [
                ['type' => 'track', 'liked' => false, 'track' => ['id' => 31190260, 'title' => 'Нирвана']],
                ['type' => 'track', 'liked' => false, 'track' => ['id' => 31190261, 'title' => 'Тёмный рыцарь']],
                // An advertisement carries no track, and must not become one.
                ['type' => 'ad'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['batchId' => '1789647430947178-11328995973787230000.QxPI'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(StationTracksResult::class, $model);
        self::assertSame('genre:allrock', $model->id?->tag());
        self::assertSame('1789647430947178-11328995973787230000.QxPI', $model->batchId);
        self::assertSame('aBcD-1234', $model->radioSessionId);
        self::assertFalse($model->pumpkin);
        self::assertCount(3, $model->sequence);
        self::assertInstanceOf(Sequence::class, $model->sequence[0]);
    }

    /**
     * The tracks are what a caller wants; the entries without one are not
     * tracks at all.
     */
    public function testTracksSkipTheEntriesWithout(): void
    {
        $model = StationTracksResult::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(StationTracksResult::class, $model);

        $tracks = $model->tracks();

        self::assertCount(2, $tracks);
        self::assertInstanceOf(Track::class, $tracks[0]);
        self::assertSame('Тёмный рыцарь', $tracks[1]->title);
    }

    protected function equalityTriple(): array
    {
        return [
            new StationTracksResult(new Id('genre', 'allrock'), batchId: 'b1'),
            new StationTracksResult(new Id('genre', 'allrock'), [new Sequence('track')], 'b1'),
            new StationTracksResult(new Id('genre', 'allrock'), batchId: 'b2'),
        ];
    }
}
