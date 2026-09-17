<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Skeleton\SkeletonBlock;

/**
 * The layout of a concert's page — the same five skeleton models an artist's
 * page uses.
 */
final class ConcertSkeleton extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'blocks' => [SkeletonBlock::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        /** @var list<SkeletonBlock> */
        public readonly array $blocks = [],
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id];
    }
}
