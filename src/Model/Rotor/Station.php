<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Icon;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A radio station: what it is called, how it looks, and what it can be tuned
 * to.
 */
final class Station extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'id' => [Id::class, 'one'],
        'parentId' => [Id::class, 'one'],
        'icon' => [Icon::class, 'one'],
        'mtsIcon' => [Icon::class, 'one'],
        'geocellIcon' => [Icon::class, 'one'],
        'restrictions' => [Restrictions::class, 'one'],
        'restrictions2' => [Restrictions::class, 'one'],
    ];

    public function __construct(
        public readonly ?Id $id = null,
        public readonly ?string $name = null,
        public readonly ?Icon $icon = null,
        public readonly ?Icon $mtsIcon = null,
        public readonly ?Icon $geocellIcon = null,
        public readonly ?string $idForFrom = null,
        public readonly ?Restrictions $restrictions = null,
        /** The same restrictions in a newer arrangement, without the scales. */
        public readonly ?Restrictions $restrictions2 = null,
        public readonly ?string $fullImageUrl = null,
        public readonly ?string $mtsFullImageUrl = null,
        public readonly ?Id $parentId = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->name];
    }
}
