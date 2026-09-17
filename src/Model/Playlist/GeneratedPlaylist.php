<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Playlist;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A playlist the service generated for the account — the daily playlist, the
 * weekly release digest, and their kin.
 *
 * The playlist proper sits in $data; the fields around it say what kind it
 * is and whether it is worth showing yet.
 */
final class GeneratedPlaylist extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'data' => [Playlist::class, 'one'],
    ];

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?bool $ready = null,
        public readonly ?bool $notify = null,
        public readonly ?Playlist $data = null,
        /** @var list<mixed> Not modelled: a list whose shape the reference leaves open. */
        public readonly array $description = [],
        public readonly ?string $previewDescription = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->type, $this->data];
    }
}
