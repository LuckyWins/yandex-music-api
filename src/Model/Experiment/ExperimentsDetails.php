<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Experiment;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The A/B experiments an account is in, keyed by experiment name.
 *
 * The endpoint answers with the map itself rather than wrapping it in a field,
 * so the whole response body is the value of `experiments`. Which names appear
 * is Yandex's business and changes between releases — there were over seven
 * hundred of them when this was written.
 */
final class ExperimentsDetails extends Model
{
    public function __construct(
        /** @var array<string, ExperimentDetail> */
        public readonly array $experiments = [],
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The response is the map, so it cannot be declared through NESTED: there
     * is no key to hang it on.
     */
    public static function fromApi(mixed $data, ?Client $client = null): ?static
    {
        if (!is_array($data) || [] === $data) {
            return null;
        }

        return new static(ExperimentDetail::mapFromApi($data, $client), $client);
    }
}
