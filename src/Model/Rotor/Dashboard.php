<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Rotor;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The stations offered to this account, in the order they should be shown.
 */
final class Dashboard extends Model
{
    /** @var array<string, array{0: class-string<Model>, 1: 'one'|'list'}> */
    protected const NESTED = [
        'stations' => [StationResult::class, 'list'],
    ];

    public function __construct(
        public readonly ?string $dashboardId = null,
        /** @var list<StationResult> */
        public readonly array $stations = [],
        public readonly ?bool $pumpkin = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [$this->dashboardId];
    }
}
