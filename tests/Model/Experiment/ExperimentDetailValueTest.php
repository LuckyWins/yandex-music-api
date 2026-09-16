<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Experiment;

use LuckyWins\YandexMusic\Model\Experiment\ExperimentDetailValue;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use LuckyWins\YandexMusic\Tests\Support\RecordingLogger;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExperimentDetailValue::class)]
final class ExperimentDetailValueTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ExperimentDetailValue::class;
    }

    protected static function fullPayload(): array
    {
        return ['title' => 'variant B', 'timeout' => 3000, 'enabled' => true, 'weights' => [1, 2]];
    }

    protected static function requiredPayload(): array
    {
        return ['title' => 'variant B'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ExperimentDetailValue::class, $model);
        self::assertSame('variant B', $model->title);

        // Each experiment configures itself differently, so everything is kept.
        self::assertSame(
            ['title' => 'variant B', 'timeout' => 3000, 'enabled' => true, 'weights' => [1, 2]],
            $model->parameters,
        );
    }

    protected function equalityTriple(): array
    {
        return [
            new ExperimentDetailValue('control'),
            new ExperimentDetailValue('control'),
            new ExperimentDetailValue('variant'),
        ];
    }

    /**
     * Per-experiment settings are data, not fields this model failed to
     * declare, so they must not be reported as unknown.
     */
    public function testExperimentSettingsAreNotReportedAsUnknownFields(): void
    {
        $logger = new RecordingLogger();

        ExperimentDetailValue::fromApi(
            ['title' => 'x', 'someSettingNobodyDeclared' => 42],
            self::clientReportingUnknownFields($logger),
        );

        self::assertSame([], $logger->unknownFields());
    }
}
