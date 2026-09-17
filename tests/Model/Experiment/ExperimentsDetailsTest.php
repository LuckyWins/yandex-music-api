<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Experiment;

use LuckyWins\YandexMusic\Model\Experiment\ExperimentDetail;
use LuckyWins\YandexMusic\Model\Experiment\ExperimentsDetails;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use LuckyWins\YandexMusic\Tests\Support\RecordingLogger;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * The endpoint answers with the experiment map itself rather than wrapping it,
 * so the whole body is the data. Getting that wrong turns every experiment name
 * into an unknown field.
 */
#[CoversClass(ExperimentsDetails::class)]
final class ExperimentsDetailsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return ExperimentsDetails::class;
    }

    protected static function fullPayload(): array
    {
        return [
            'AndroidNewLanding' => ['group' => 'test', 'value' => ['title' => 'variant B']],
            'fastSearch' => ['group' => 'control'],
        ];
    }

    protected static function requiredPayload(): array
    {
        return ['fastSearch' => ['group' => 'control']];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(ExperimentsDetails::class, $model);
        self::assertCount(2, $model->experiments);

        // Names are the keys, verbatim, including their casing.
        self::assertSame(['AndroidNewLanding', 'fastSearch'], array_keys($model->experiments));

        $landing = $model->experiments['AndroidNewLanding'];
        self::assertInstanceOf(ExperimentDetail::class, $landing);
        self::assertSame('test', $landing->group);
        self::assertSame('variant B', $landing->value?->title);

        self::assertNull($model->experiments['fastSearch']->value);
    }

    protected function equalityTriple(): array
    {
        // Nothing to key on, so equality is object identity.
        $a = new ExperimentsDetails();

        return [$a, $a, new ExperimentsDetails()];
    }

    public function testUnusableEntriesAreSkipped(): void
    {
        $model = ExperimentsDetails::fromApi(
            ['good' => ['group' => 'a'], 'bad' => 'not an object', 'empty' => []],
            self::client(),
        );

        self::assertInstanceOf(ExperimentsDetails::class, $model);
        self::assertSame(['good'], array_keys($model->experiments));
    }

    /**
     * An experiment name must never be mistaken for a field of this model.
     */
    public function testExperimentNamesAreNotReportedAsUnknownFields(): void
    {
        $logger = new RecordingLogger();

        ExperimentsDetails::fromApi(
            ['experiments' => ['group' => 'a'], 'AliceTest' => ['group' => 'b']],
            self::clientReportingUnknownFields($logger),
        );

        self::assertSame([], $logger->unknownFields());
    }
}
