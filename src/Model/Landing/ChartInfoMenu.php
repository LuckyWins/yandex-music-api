<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The charts on offer beside the one being shown.
 */
final class ChartInfoMenu extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'items' => [ChartInfoMenuItem::class, 'list'],
    ];

    public function __construct(
        /** @var list<ChartInfoMenuItem> */
        public readonly array $items = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The chart currently being shown, if the menu says.
     */
    public function selected(): ?ChartInfoMenuItem
    {
        foreach ($this->items as $item) {
            if (true === $item->selected) {
                return $item;
            }
        }

        return null;
    }

    protected function identity(): array
    {
        return [$this->items];
    }
}
