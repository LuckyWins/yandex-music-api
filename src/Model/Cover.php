<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model;

use LuckyWins\YandexMusic\Client;

/**
 * Artwork, as a template rather than a finished URL.
 *
 * `uri` carries a `%%` where the size belongs, so one cover serves every
 * dimension the interface asks for. Use url() rather than building it by hand.
 */
final class Cover extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'derivedColors' => [CoverDerivedColors::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $uri = null,
        /** @var list<string>|null */
        public readonly ?array $itemsUri = null,
        public readonly ?string $dir = null,
        public readonly ?string $version = null,
        public readonly ?bool $custom = null,
        public readonly ?bool $isCustom = null,
        public readonly ?string $copyrightName = null,
        public readonly ?string $copyrightCline = null,
        public readonly ?string $prefix = null,
        public readonly ?string $error = null,
        public readonly ?string $color = null,
        public readonly ?CoverDerivedColors $derivedColors = null,
        public readonly ?string $videoUrl = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The cover at a given size, such as `400x400` or `orig`.
     *
     * Returns null when there is no template to fill in.
     */
    public function url(string $size = '200x200'): ?string
    {
        if (null === $this->uri) {
            return null;
        }

        return 'https://'.str_replace('%%', $size, $this->uri);
    }

    protected function identity(): array
    {
        return [$this->prefix, $this->version, $this->uri, $this->itemsUri];
    }
}
