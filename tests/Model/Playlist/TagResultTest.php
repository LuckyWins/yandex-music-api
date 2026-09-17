<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Playlist;

use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistId;
use LuckyWins\YandexMusic\Model\Playlist\TagResult;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TagResult::class)]
final class TagResultTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return TagResult::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'tag' => 'rock',
            'ids' => [
                ['uid' => 503646255, 'kind' => 1042],
                ['uid' => 503646255, 'kind' => 1043],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['tag' => 'rock'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(TagResult::class, $model);
        self::assertSame('rock', $model->tag);
        self::assertCount(2, $model->ids);
        self::assertInstanceOf(PlaylistId::class, $model->ids[0]);
    }

    /**
     * The references come out in the form playlistsList() takes, which is the
     * point of fetching them at all.
     */
    public function testThePairsAreReadyForTheBatchEndpoint(): void
    {
        $model = TagResult::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(TagResult::class, $model);
        self::assertSame(['503646255:1042', '503646255:1043'], $model->pairs());
    }

    public function testAHalfIdentifiedPlaylistIsSkipped(): void
    {
        $model = TagResult::fromApi(['tag' => 'rock', 'ids' => [['kind' => 1042]]], self::client());

        self::assertInstanceOf(TagResult::class, $model);
        self::assertCount(1, $model->ids);
        self::assertSame([], $model->pairs());
    }

    protected function equalityTriple(): array
    {
        return [new TagResult('rock'), new TagResult('rock'), new TagResult('rap')];
    }
}
