<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Pin;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * Everything the account has pinned, in the order it is shown.
 */
final class PinsList extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'pins' => [Pin::class, 'list'],
    ];

    public function __construct(
        /** @var list<Pin> */
        public readonly array $pins = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The pins of one kind.
     *
     * The service names the kinds `album_item`, `artist_item`,
     * `playlist_item` and `wave_item`, while the endpoints that create them
     * are `/pin/album` and so on. Both spellings work here, because being
     * handed an empty list for asking the obvious way is no use to anyone.
     *
     * @return list<Pin>
     */
    public function ofType(string $type): array
    {
        $wanted = [$type, $type.'_item'];

        return array_values(array_filter(
            $this->pins,
            static fn (Pin $pin): bool => in_array($pin->type, $wanted, true),
        ));
    }

    protected function identity(): array
    {
        return [$this->pins];
    }
}
