<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Examples\Support;

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * Collects the fields the API sent that no model declares.
 *
 * Pass one to Bootstrap::authorizedClient() and the client starts reporting;
 * print the summary at the end of a run to see whether the models have fallen
 * behind the API. An empty summary is the result to want.
 *
 * Names and types only. The values are the account's own data, and the point
 * of this is to be safe to paste into an issue.
 */
final class UnknownFieldCollector extends AbstractLogger
{
    /** @var array<string, array<string, string>> model to field to type */
    private array $fields = [];

    /** @var array<string, array<string, array<string, true>>> model to field to the calls it arrived on */
    private array $sources = [];

    private ?string $during = null;

    /**
     * Name the call whose responses are about to be read.
     *
     * A field's name and shape say what to write; where it came from says
     * which response to go and look at. Without this, a field seen once during
     * a hundred calls sends you hunting through all hundred — which is how an
     * hour went once.
     */
    public function during(?string $endpoint): void
    {
        $this->during = $endpoint;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $model = $context['model'] ?? null;
        $fields = $context['fields'] ?? null;

        if (!is_string($model) || !is_array($fields)) {
            return;
        }

        $types = $context['types'] ?? [];

        if (!is_array($types)) {
            $types = [];
        }

        foreach ($fields as $field) {
            if (!is_string($field)) {
                continue;
            }

            // A field seen twice with two types is worth knowing about — it
            // means the API is inconsistent about it — so both are kept.
            $type = $types[$field] ?? null;
            $type = is_string($type) ? $type : 'unknown';
            $seen = $this->fields[$model][$field] ?? null;

            if (null !== $seen && !in_array($type, explode('|', $seen), true)) {
                $type = $seen.'|'.$type;
            } elseif (null !== $seen) {
                $type = $seen;
            }

            $this->fields[$model][$field] = $type;

            if (null !== $this->during) {
                $this->sources[$model][$field][$this->during] = true;
            }
        }
    }

    /**
     * Every model that sent something unknown, and what it sent.
     *
     * @return array<string, array<string, string>>
     */
    public function all(): array
    {
        return $this->fields;
    }

    /**
     * One line per model that sent something unknown, or a single line saying
     * nothing did.
     */
    public function summary(): string
    {
        if ([] === $this->fields) {
            return "no unknown fields: every field the API sent has a home\n";
        }

        $lines = '';

        foreach ($this->fields as $model => $fields) {
            $short = substr((string) strrchr('\\'.$model, '\\'), 1);
            $lines .= sprintf("  %s\n", $short);

            foreach ($fields as $field => $type) {
                $calls = array_keys($this->sources[$model][$field] ?? []);

                $lines .= rtrim(sprintf(
                    '    %-28s %-18s %s',
                    $field,
                    $type,
                    [] === $calls ? '' : 'seen on '.implode(', ', array_slice($calls, 0, 4)),
                ))."\n";
            }
        }

        return "unknown fields, meaning the models are behind the API:\n".$lines;
    }
}
