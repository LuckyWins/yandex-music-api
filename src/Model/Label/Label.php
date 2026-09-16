<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Label;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Artist\Link;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A record label.
 */
final class Label extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'links' => [Link::class, 'list'],
    ];

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?string $descriptionFormatted = null,
        public readonly ?string $image = null,
        /** @var list<Link>|null */
        public readonly ?array $links = null,
        public readonly ?string $type = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->id, $this->name];
    }
}
