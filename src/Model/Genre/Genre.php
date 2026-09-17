<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Genre;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Icon;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A genre, and the genres inside it.
 *
 * $titles is keyed by language rather than being a list, so a name can be
 * looked up directly: `$genre->titles['ru']?->title`.
 */
final class Genre extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'images' => [Images::class, 'one'],
        'radioIcon' => [Icon::class, 'one'],
        'subGenres' => [self::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $title = null,
        public readonly ?string $fullTitle = null,
        /** @var array<string, Title> keyed by language */
        public readonly array $titles = [],
        public readonly ?int $weight = null,
        public readonly ?bool $composerTop = null,
        public readonly ?bool $showInMenu = null,
        /** @var list<int> */
        public readonly array $showInRegions = [],
        /** @var list<int> regions this genre is kept out of */
        public readonly array $hideInRegions = [],
        public readonly ?string $urlPart = null,
        public readonly ?string $color = null,
        public readonly ?Images $images = null,
        public readonly ?Icon $radioIcon = null,
        /** @var list<self> */
        public readonly array $subGenres = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The genre's name in a language, falling back to the default title.
     */
    public function titleIn(string $language): ?string
    {
        return $this->titles[$language]->title ?? $this->title;
    }

    /**
     * A map rather than a list, which NESTED cannot express.
     */
    protected static function prepare(array $args, array $data, ?Client $client): array
    {
        $args['titles'] = Title::mapFromApi($data['titles'] ?? null, $client);

        return $args;
    }

    protected function identity(): array
    {
        return [$this->id];
    }
}
