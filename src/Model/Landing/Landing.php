<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The front page: whichever blocks were asked for, in the order to show them.
 */
final class Landing extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'blocks' => [Block::class, 'list'],
    ];

    public function __construct(
        public readonly ?bool $pumpkin = null,
        public readonly string|int|null $contentId = null,
        /** @var list<Block> */
        public readonly array $blocks = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * One block by its type, or null when it was not asked for or came back
     * empty.
     */
    public function block(BlockType|string $type): ?Block
    {
        $wanted = $type instanceof BlockType ? $type->value : $type;

        foreach ($this->blocks as $block) {
            if ($block->type === $wanted) {
                return $block;
            }
        }

        return null;
    }

    protected function identity(): array
    {
        return [$this->contentId];
    }
}
