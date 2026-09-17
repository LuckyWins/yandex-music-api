<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Metatag;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One tree of tags — a way of navigating them, such as by mood or by era.
 */
final class MetatagTree extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'leaves' => [MetatagLeaf::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $navigationId = null,
        /** @var list<MetatagLeaf> */
        public readonly array $leaves = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->navigationId, $this->title];
    }
}
