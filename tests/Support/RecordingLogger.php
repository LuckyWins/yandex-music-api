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

    /**
     * @param array<string, mixed> $context
     */
    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        $fields = $context['fields'] ?? null;

        if (!is_array($fields)) {
            return;
        }

        foreach ($fields as $field) {
            if (is_string($field)) {
                $this->unknownFields[] = $field;
            }
        }
    }

    /** @return list<string> */
    public function unknownFields(): array
    {
        return $this->unknownFields;
    }
}
