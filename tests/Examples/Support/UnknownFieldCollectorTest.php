<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Examples\Support;

use LuckyWins\YandexMusic\Examples\Support\UnknownFieldCollector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

/**
 * The collector gathers a whole run's worth of reports into one summary.
 *
 * It is what makes `examples/audit.php` readable: one line per model rather
 * than a warning per response.
 */
#[CoversClass(UnknownFieldCollector::class)]
final class UnknownFieldCollectorTest extends TestCase
{
    public function testFieldsAreGatheredPerModelWithTheirTypes(): void
    {
        $collector = new UnknownFieldCollector();

        self::report($collector, 'App\\Model\\Track', ['migrated' => 'bool', 'shots' => 'list<object>']);
        self::report($collector, 'App\\Model\\Album', ['deprecated' => 'bool']);

        self::assertSame([
            'App\\Model\\Track' => ['migrated' => 'bool', 'shots' => 'list<object>'],
            'App\\Model\\Album' => ['deprecated' => 'bool'],
        ], $collector->all());
    }

    /**
     * The same field arriving from a hundred responses is one finding, not a
     * hundred.
     */
    public function testAFieldSeenAgainIsNotRecordedTwice(): void
    {
        $collector = new UnknownFieldCollector();

        self::report($collector, 'App\\Model\\Track', ['migrated' => 'bool']);
        self::report($collector, 'App\\Model\\Track', ['migrated' => 'bool']);

        self::assertSame(['App\\Model\\Track' => ['migrated' => 'bool']], $collector->all());
    }

    /**
     * A field that is a string here and an object there is worth knowing
     * about: it means the field cannot simply be declared as one or the other.
     */
    public function testAFieldThatChangesShapeKeepsBothShapes(): void
    {
        $collector = new UnknownFieldCollector();

        self::report($collector, 'App\\Model\\Track', ['cover' => 'string']);
        self::report($collector, 'App\\Model\\Track', ['cover' => 'object']);

        self::assertSame(['App\\Model\\Track' => ['cover' => 'string|object']], $collector->all());
    }

    /**
     * Older reports, or another library's, carry names without types.
     */
    public function testAReportWithoutTypesStillCounts(): void
    {
        $collector = new UnknownFieldCollector();

        $collector->log(LogLevel::WARNING, 'unknown fields', [
            'model' => 'App\\Model\\Track',
            'fields' => ['migrated'],
        ]);

        self::assertSame(['App\\Model\\Track' => ['migrated' => 'unknown']], $collector->all());
    }

    public function testAnythingThatIsNotSuchAReportIsIgnored(): void
    {
        $collector = new UnknownFieldCollector();

        $collector->log(LogLevel::ERROR, 'something else entirely');
        $collector->log(LogLevel::WARNING, 'half a report', ['model' => 'App\\Model\\Track']);

        self::assertSame([], $collector->all());
    }

    public function testTheSummaryOfAQuietRunSaysSo(): void
    {
        self::assertStringContainsString('no unknown fields', (new UnknownFieldCollector())->summary());
    }

    public function testTheSummaryNamesTheModelTheFieldAndTheType(): void
    {
        $collector = new UnknownFieldCollector();

        self::report($collector, 'App\\Model\\Track', ['migrated' => 'bool']);

        $summary = $collector->summary();

        self::assertStringContainsString('Track', $summary);
        self::assertMatchesRegularExpression('/migrated\s+bool/', $summary);
        self::assertStringNotContainsString('App\\Model\\Track', $summary, 'the namespace is noise in a summary');
    }

    /**
     * The name and the shape say what to write; the call says which response
     * to go and read. A field that turns up once in a hundred calls is
     * otherwise a hunt through all hundred.
     */
    public function testTheCallAFieldArrivedOnIsRemembered(): void
    {
        $collector = new UnknownFieldCollector();

        $collector->during('albumsWithTracks');
        self::report($collector, 'App\\Model\\Album', ['albumType' => 'string']);
        $collector->during(null);

        self::assertStringContainsString('seen on albumsWithTracks', $collector->summary());
    }

    public function testAFieldArrivingOnSeveralCallsNamesThemAll(): void
    {
        $collector = new UnknownFieldCollector();

        foreach (['album', 'albumsWithTracks'] as $call) {
            $collector->during($call);
            self::report($collector, 'App\\Model\\Album', ['albumType' => 'string']);
        }

        self::assertStringContainsString('seen on album, albumsWithTracks', $collector->summary());
    }

    /**
     * Reports that arrive outside any call are still worth keeping — they just
     * cannot say where from.
     */
    public function testAFieldWithNoCallInProgressIsStillRecorded(): void
    {
        $collector = new UnknownFieldCollector();

        self::report($collector, 'App\\Model\\Album', ['albumType' => 'string']);

        self::assertSame(['App\\Model\\Album' => ['albumType' => 'string']], $collector->all());
        self::assertStringNotContainsString('seen on', $collector->summary());
    }

    /**
     * @param array<string, string> $types
     */
    private static function report(UnknownFieldCollector $collector, string $model, array $types): void
    {
        $collector->log(LogLevel::WARNING, 'unknown fields', [
            'model' => $model,
            'fields' => array_keys($types),
            'types' => $types,
        ]);
    }
}
