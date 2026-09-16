<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Experiment;

use LuckyWins\YandexMusic\Model\Experiment\ExperimentDetail;
use LuckyWins\YandexMusic\Model\Experiment\ExperimentDetailValue;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExperimentDetail::class)]
final class ExperimentDetailTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ExperimentDetail::class;
    }

    protected static function fullPayload(): array
    {
        return ['group' => 'test', 'value' => ['title' => 'variant B']];
    }

    protected static function requiredPayload(): array
    {
        return ['group' => 'test'];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ExperimentDetail::class, $model);
        self::assertSame('test', $model->group);
        self::assertInstanceOf(ExperimentDetailValue::class, $model->value);
        self::assertSame('variant B', $model->value->title);
    }

    protected function equalityTriple(): array
    {
        return [
            new ExperimentDetail('test'),
            new ExperimentDetail('test'),
            new ExperimentDetail('control'),
        ];
    }
}
