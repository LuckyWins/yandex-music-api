<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Landing;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;

/**
 * Somewhere the account was listening, so it can be picked up again.
 */
final class PlayContext extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'tracks' => [TrackShortOld::class, 'list'],
    ];

    /**
     * What was being listened to, by the kind of context it was.
     *
     * @var array<string, class-string<Model>>
     */
    private const PAYLOADS = [
        'playlist' => Playlist::class,
        'album' => Album::class,
        'artist' => Artist::class,
    ];

    public function __construct(
        /** The client it was played in; `client` on the wire, renamed to leave that name to the back-reference. */
        public readonly ?string $playedIn = null,
        public readonly ?string $context = null,
        public readonly ?string $contextItem = null,
        /** @var list<TrackShortOld> */
        public readonly array $tracks = [],
        /** What was being listened to: whichever kind $context names. */
        public readonly Album|Artist|Playlist|null $payload = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The wire calls this field `client`, which is the name the back-reference
     * to the API client already has. Renaming the property would normally be
     * enough, but `client` is filled in by the deserializer itself, so the
     * value has to be moved by hand.
     */
    protected static function prepare(array $args, array $data, ?Client $client): array
    {
        $playedIn = $data['client'] ?? null;
        $args['playedIn'] = is_string($playedIn) ? $playedIn : null;

        // The payload is a playlist, an album or an artist, and only $context
        // says which. Reading it as the wrong one would mean pouring an album
        // into a playlist, which is exactly what the unknown-field report
        // caught when this was assumed to be a playlist always.
        $context = $data['context'] ?? null;
        $model = is_string($context) ? (self::PAYLOADS[$context] ?? null) : null;

        $args['payload'] = null === $model ? null : $model::fromApi($data['payload'] ?? null, $client);

        return $args;
    }

    protected function identity(): array
    {
        return [$this->context, $this->contextItem];
    }
}
