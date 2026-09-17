<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Metatag;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Every way the catalogue is tagged.
 */
final class Metatags extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'trees' => [MetatagTree::class, 'list'],
    ];

    public function __construct(
        /** @var list<MetatagTree> */
        public readonly array $trees = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Every tag in every tree, flattened.
     *
     * @return list<MetatagLeaf>
     */
    public function tags(): array
    {
        $found = [];

        foreach ($this->trees as $tree) {
            foreach ($tree->leaves as $leaf) {
                $found = [...$found, ...$leaf->flatten()];
            }
        }

        return $found;
    }

    protected function identity(): array
    {
        return [$this->trees];
    }
}
