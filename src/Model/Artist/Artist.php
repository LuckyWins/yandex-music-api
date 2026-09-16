<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\ContentRestrictions;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * An artist.
 *
 * `decomposed` resists declaration: it is a credit line split into pieces,
 * mixing artists with the words joining them, so its elements have no single
 * type. See prepare() below.
 */
final class Artist extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'cover' => [Cover::class, 'one'],
        'cutoutCover' => [Cover::class, 'one'],
        'counts' => [Counts::class, 'one'],
        'ratings' => [Ratings::class, 'one'],
        'links' => [Link::class, 'list'],
        'popularTracks' => [Track::class, 'list'],
        'description' => [Description::class, 'one'],
        'contentRestrictions' => [ContentRestrictions::class, 'one'],
    ];

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $error = null,
        public readonly ?string $reason = null,
        public readonly ?string $name = null,
        public readonly ?Cover $cover = null,
        /** Whether this stands for an assortment of artists rather than one. */
        public readonly ?bool $various = null,
        public readonly ?bool $composer = null,
        /** @var list<string>|null */
        public readonly ?array $genres = null,
        public readonly ?string $ogImage = null,
        public readonly ?string $opImage = null,
        /** Set only when the artist comes back from a search. */
        public readonly mixed $noPicturesFromSearch = null,
        public readonly ?Counts $counts = null,
        public readonly ?bool $available = null,
        public readonly ?Ratings $ratings = null,
        /** @var list<Link>|null */
        public readonly ?array $links = null,
        public readonly ?bool $ticketsAvailable = null,
        public readonly ?int $likesCount = null,
        /** @var list<Track>|null */
        public readonly ?array $popularTracks = null,
        /** @var list<string>|null */
        public readonly ?array $regions = null,
        /**
         * A credit line broken into pieces: artists interleaved with the words
         * that join them, such as `feat.`. Render it by concatenating in order.
         *
         * @var list<Artist|string>|null
         */
        public readonly ?array $decomposed = null,
        public readonly mixed $fullNames = null,
        public readonly ?string $handMadeDescription = null,
        public readonly ?Description $description = null,
        /** @var list<string>|null */
        public readonly ?array $countries = null,
        public readonly ?string $enWikipediaLink = null,
        /** @var list<string>|null */
        public readonly ?array $dbAliases = null,
        public readonly mixed $aliases = null,
        public readonly ?string $initDate = null,
        public readonly ?string $endDate = null,
        public readonly ?string $yaMoneyId = null,
        /** @var list<string>|null */
        public readonly ?array $disclaimers = null,
        public readonly ?ContentRestrictions $contentRestrictions = null,
        public readonly ?Cover $cutoutCover = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Resolve the credit line, element by element.
     *
     * Each entry is either a joining word or another artist, and only the
     * content says which.
     */
    protected static function prepare(array $args, array $data, ?Client $client): array
    {
        $raw = $data['decomposed'] ?? null;

        if (!is_array($raw)) {
            return $args;
        }

        $decomposed = [];

        foreach ($raw as $part) {
            $resolved = is_array($part) ? self::fromApi($part, $client) : $part;

            if (is_string($resolved) || $resolved instanceof self) {
                $decomposed[] = $resolved;
            }
        }

        $args['decomposed'] = $decomposed;

        return $args;
    }

    /**
     * The credit line as a single string, joining words included.
     */
    public function creditLine(): string
    {
        if (null === $this->decomposed || [] === $this->decomposed) {
            return $this->name ?? '';
        }

        $parts = [];

        foreach ($this->decomposed as $part) {
            $parts[] = is_string($part) ? $part : ($part->name ?? '');
        }

        return implode(' ', array_filter($parts, static fn (string $p): bool => '' !== trim($p)));
    }

    protected function identity(): array
    {
        return [$this->id, $this->name];
    }
}
