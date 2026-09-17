<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Landing;

use LuckyWins\YandexMusic\Model\Landing\Block;
use LuckyWins\YandexMusic\Model\Landing\BlockType;
use LuckyWins\YandexMusic\Model\Landing\Landing;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Landing::class)]
final class LandingTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Landing::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'pumpkin' => false,
            'contentId' => '10254713668400548221',
            'blocks' => [
                ['id' => 'b1', 'type' => 'personalplaylists', 'title' => 'Собрано для вас'],
                ['id' => 'b2', 'type' => 'chart', 'title' => 'Чарт'],
            ],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['contentId' => '10254713668400548221'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Landing::class, $model);
        self::assertFalse($model->pumpkin);
        self::assertSame('10254713668400548221', $model->contentId);
        self::assertCount(2, $model->blocks);
        self::assertInstanceOf(Block::class, $model->blocks[0]);
    }

    public function testABlockCanBeFoundByType(): void
    {
        $model = Landing::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(Landing::class, $model);
        self::assertSame('Чарт', $model->block(BlockType::Chart)?->title);
        self::assertSame('Чарт', $model->block('chart')?->title, 'a bare string works too');
        self::assertNull($model->block(BlockType::Mixes), 'not asked for, so not there');
    }

    protected function equalityTriple(): array
    {
        return [new Landing(contentId: 'c1'), new Landing(true, 'c1'), new Landing(contentId: 'c2')];
    }
}
