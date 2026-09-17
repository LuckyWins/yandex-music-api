<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Metatag;

use LuckyWins\YandexMusic\Model\Metatag\MetatagLeaf;
use LuckyWins\YandexMusic\Model\Metatag\Metatags;
use LuckyWins\YandexMusic\Model\Metatag\MetatagTree;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Metatags::class)]
#[CoversClass(MetatagTree::class)]
#[CoversClass(MetatagLeaf::class)]
final class MetatagsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return Metatags::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'trees' => [[
                'title' => 'Настроение и занятие',
                'navigationId' => 'mood',
                'leaves' => [
                    [
                        'tag' => 'activity',
                        'title' => 'Занятие',
                        'leaves' => [
                            ['tag' => 'run', 'title' => 'Для бега'],
                            ['tag' => 'sleep', 'title' => 'Для сна', 'leaves' => [['tag' => 'lullaby']]],
                        ],
                    ],
                ],
            ]],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['trees' => [['navigationId' => 'mood']]];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(Metatags::class, $model);
        self::assertCount(1, $model->trees);
        self::assertInstanceOf(MetatagTree::class, $model->trees[0]);
        self::assertSame('mood', $model->trees[0]->navigationId);
        self::assertCount(1, $model->trees[0]->leaves);
        self::assertInstanceOf(MetatagLeaf::class, $model->trees[0]->leaves[0]);
        self::assertCount(2, $model->trees[0]->leaves[0]->leaves);
    }

    /**
     * Tags nest, and what a caller usually wants is all of them rather than
     * the shape they arrived in.
     */
    public function testTheTreeFlattens(): void
    {
        $model = Metatags::fromApi(self::fullPayload(), self::client());

        self::assertInstanceOf(Metatags::class, $model);

        $tags = array_map(static fn (MetatagLeaf $leaf): ?string => $leaf->tag, $model->tags());

        self::assertSame(['activity', 'run', 'sleep', 'lullaby'], $tags);
    }

    public function testALeafWithoutChildrenIsItsOwnFlattening(): void
    {
        $leaf = MetatagLeaf::fromApi(['tag' => 'run', 'title' => 'Для бега'], self::client());

        self::assertInstanceOf(MetatagLeaf::class, $leaf);
        self::assertCount(1, $leaf->flatten());
        self::assertSame($leaf, $leaf->flatten()[0]);
    }

    protected function equalityTriple(): array
    {
        return [
            new Metatags([new MetatagTree('Настроение', 'mood')]),
            new Metatags([new MetatagTree('Настроение', 'mood')]),
            new Metatags([new MetatagTree('Эпоха', 'era')]),
        ];
    }
}
