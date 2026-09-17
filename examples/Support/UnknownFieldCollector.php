<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Examples;

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * Collects the fields the API sent that no model declares.
 *
 * Pass one to Bootstrap::authorizedClient() and the client starts reporting;
 * print the summary at the end of a run to see whether the models have fallen
 * behind the API. An empty summary is the result to want.
 */
final class UnknownFieldCollector extends AbstractLogger
{
    /** @var array<string, list<string>> */
    private array $fields = [];

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

        foreach ($fields as $field) {
            if (is_string($field) && !in_array($field, $this->fields[$model] ?? [], true)) {
                $this->fields[$model][] = $field;
            }
        }
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
            $lines .= sprintf("  %s: %s\n", $short, implode(', ', $fields));
        }

        return "unknown fields, meaning the models are behind the API:\n".$lines;
    }
}
