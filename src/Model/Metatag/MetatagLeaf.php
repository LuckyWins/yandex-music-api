<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Metatag;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A branch of the tag tree, holding branches of its own.
 */
final class MetatagLeaf extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'leaves' => [self::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $tag = null,
        public readonly ?string $title = null,
        /** @var list<self> */
        public readonly array $leaves = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Every tag under this one, however deep, including this one.
     *
     * @return list<self>
     */
    public function flatten(): array
    {
        $found = [$this];

        foreach ($this->leaves as $leaf) {
            $found = [...$found, ...$leaf->flatten()];
        }

        return $found;
    }

    protected function identity(): array
    {
        return [$this->tag];
    }
}
