<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * A station together with how it is tuned right now.
 *
 * The listing endpoints answer with these rather than with bare stations,
 * because the settings are per account.
 */
final class StationResult extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'station' => [Station::class, 'one'],
        'settings' => [RotorSettings::class, 'one'],
        'settings2' => [RotorSettings::class, 'one'],
        'adParams' => [AdParams::class, 'one'],
    ];

    public function __construct(
        public readonly ?Station $station = null,
        public readonly ?RotorSettings $settings = null,
        public readonly ?RotorSettings $settings2 = null,
        public readonly ?AdParams $adParams = null,
        public readonly ?string $explanation = null,
        /** @var list<mixed> Not modelled: advertising inserts, raw in the reference too. */
        public readonly array $prerolls = [],
        public readonly ?string $rupTitle = null,
        public readonly ?string $rupDescription = null,
        public readonly ?string $customName = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->station];
    }
}
