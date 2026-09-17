<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\LandingList;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\PlaylistId;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LandingList::class)]
final class LandingListTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return LandingList::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'id' => 'new-releases',
            'type' => 'new-releases',
            'typeForFrom' => 'new-releases',
            'title' => 'Новые релизы',
            'newReleases' => [4243617, 4243618],
            'newPlaylists' => [['uid' => 1, 'kind' => 1042]],
            'podcasts' => [99],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['type' => 'new-releases'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(LandingList::class, $model);
        self::assertSame('new-releases', $model->id);
        self::assertSame('new-releases', $model->type);
        self::assertSame('Новые релизы', $model->title);
        self::assertSame([4243617, 4243618], $model->newReleases);
        self::assertCount(1, $model->newPlaylists);
        self::assertInstanceOf(PlaylistId::class, $model->newPlaylists[0]);
        self::assertSame('1:1042', $model->newPlaylists[0]->pair());
        self::assertSame([99], $model->podcasts);
    }

    /**
     * Each endpoint fills only its own list, so the others stay empty rather
     * than being invented.
     */
    public function testOnlyTheAskedForListIsFilled(): void
    {
        $model = LandingList::fromApi(['type' => 'podcasts', 'podcasts' => [99, 100]], self::client());

        self::assertInstanceOf(LandingList::class, $model);
        self::assertSame([99, 100], $model->podcasts);
        self::assertSame([], $model->newReleases);
        self::assertSame([], $model->newPlaylists);
    }

    protected function equalityTriple(): array
    {
        return [
            new LandingList('new-releases', id: 'l1'),
            new LandingList('new-releases', title: 'иначе', id: 'l1'),
            new LandingList('podcasts', id: 'l2'),
        ];
    }
}
