<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Cover;
use LuckyWins\YandexMusic\Model\Model;

/**
 * What a link to this playlist unfurls into when it is shared.
 */
final class OpenGraphData extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'image' => [Cover::class, 'one'],
    ];

    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly ?Cover $image = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->title, $this->description];
    }
}
