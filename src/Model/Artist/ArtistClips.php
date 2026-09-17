<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Artist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Pager;

/**
 * A page of an artist's clips.
 */
final class ArtistClips extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'items' => [ArtistClipItem::class, 'list'],
        'pager' => [Pager::class, 'one'],
    ];

    public function __construct(
        /** @var list<ArtistClipItem> */
        public readonly array $items = [],
        public readonly ?Pager $pager = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The clips themselves, without the wrapping.
     *
     * @return list<\LuckyWins\YandexMusic\Model\Clip\Clip>
     */
    public function clips(): array
    {
        $clips = [];

        foreach ($this->items as $item) {
            $clip = $item->data?->clip;

            if (null !== $clip) {
                $clips[] = $clip;
            }
        }

        return $clips;
    }

    protected function identity(): array
    {
        return [$this->items, $this->pager];
    }
}
