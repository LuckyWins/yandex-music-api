<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Concert;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What is on, wherever the listing was asked about.
 */
final class ConcertFeed extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'items' => [ConcertFeedItem::class, 'list'],
    ];

    public function __construct(
        /** @var list<ConcertFeedItem> */
        public readonly array $items = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The concerts themselves, without the wrapping.
     *
     * @return list<Concert>
     */
    public function concerts(): array
    {
        $concerts = [];

        foreach ($this->items as $item) {
            $concert = $item->data?->concert;

            if (null !== $concert) {
                $concerts[] = $concert;
            }
        }

        return $concerts;
    }

    protected function identity(): array
    {
        return [$this->items];
    }
}
