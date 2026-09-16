<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Experiment;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The configuration an experiment was given.
 *
 * Every experiment carries its own settings here — timeouts, feature flags,
 * layout weights, whatever that experiment needed — so the shape is different
 * for each one and cannot be modelled. `title` is the one key common enough for
 * the reference library to name; the rest is kept verbatim in `parameters`
 * rather than thrown away, because it is the whole point of the endpoint.
 */
final class ExperimentDetailValue extends Model
{
    public function __construct(
        public readonly ?string $title = null,
        /**
         * Everything the experiment was configured with, exactly as sent.
         *
         * @var array<string, mixed>
         */
        public readonly array $parameters = [],
        public readonly ?Client $client = null,
    ) {
    }

    public static function fromApi(mixed $data, ?Client $client = null): ?static
    {
        if (!is_array($data) || [] === $data) {
            return null;
        }

        $title = $data['title'] ?? null;

        /** @var array<string, mixed> $parameters */
        $parameters = array_filter($data, is_string(...), ARRAY_FILTER_USE_KEY);

        return new static(is_string($title) ? $title : null, $parameters, $client);
    }

    protected function identity(): array
    {
        return [$this->title, $this->parameters];
    }
}
