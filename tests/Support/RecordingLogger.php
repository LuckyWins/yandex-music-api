<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Support;

use Psr\Log\AbstractLogger;
use Stringable;

/**
 * Keeps the fields models could not place, so a test can assert that nothing
 * was dropped in silence.
 */
final class RecordingLogger extends AbstractLogger
{
    /** @var list<string> */
    private array $unknownFields = [];

    /** @var array<string, string> */
    private array $unknownTypes = [];

    /** @var list<string> */
    private array $models = [];

    /**
     * @param array<string, mixed> $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $fields = $context['fields'] ?? null;

        if (!is_array($fields)) {
            return;
        }

        $model = $context['model'] ?? null;

        if (is_string($model)) {
            $this->models[] = $model;
        }

        foreach ($fields as $field) {
            if (is_string($field)) {
                $this->unknownFields[] = $field;
            }
        }

        $types = $context['types'] ?? null;

        if (!is_array($types)) {
            return;
        }

        foreach ($types as $field => $type) {
            if (is_string($field) && is_string($type)) {
                $this->unknownTypes[$field] = $type;
            }
        }
    }

    /** @return list<string> */
    public function unknownFields(): array
    {
        return $this->unknownFields;
    }

    /**
     * What arrived in each unplaced field, named rather than shown.
     *
     * @return array<string, string>
     */
    public function unknownTypes(): array
    {
        return $this->unknownTypes;
    }

    /** @return list<string> */
    public function models(): array
    {
        return $this->models;
    }
}
