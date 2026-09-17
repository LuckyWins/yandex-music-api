<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Skeleton;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What a block of a page holds: its tabs, where its contents come from, and
 * how to see the rest.
 */
final class SkeletonBlockData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'tabs' => [SkeletonTab::class, 'list'],
        'source' => [SkeletonSource::class, 'one'],
        'viewAllAction' => [SkeletonViewAllAction::class, 'one'],
    ];

    public function __construct(
        /** @var list<SkeletonTab> */
        public readonly array $tabs = [],
        public readonly ?int $selectedTabIndex = null,
        public readonly ?SkeletonSource $source = null,
        public readonly ?string $title = null,
        public readonly ?string $showPolicy = null,
        public readonly ?SkeletonViewAllAction $viewAllAction = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->title, $this->source];
    }
}
