<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Examples\Support;

use LuckyWins\YandexMusic\Examples\Support\Sweep;
use LuckyWins\YandexMusic\Examples\Support\UnknownFieldCollector;
use LuckyWins\YandexMusic\Exception\NotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * The bookkeeping behind `examples/audit.php`.
 *
 * The sweep itself cannot be tested — it talks to the live API — but the part
 * that decides whether the sweep is still complete can be, and it is the part
 * worth testing: a sweep that quietly stops covering an endpoint added later
 * would still print a clean report.
 */
#[CoversClass(Sweep::class)]
final class SweepTest extends TestCase
{
    public function testTheEndpointsAreTheClientsOwnMethods(): void
    {
        $endpoints = Sweep::endpoints();

        self::assertContains('accountStatus', $endpoints);
        self::assertContains('search', $endpoints);
        self::assertContains('rotorStationTracks', $endpoints);
    }

    /**
     * The client's own accessors are not endpoints, and counting them would
     * make the coverage figure a lie.
     */
    public function testTheClientsOwnAccessorsAreNotEndpoints(): void
    {
        $endpoints = Sweep::endpoints();

        self::assertNotContains('getToken', $endpoints);
        self::assertNotContains('setToken', $endpoints);
        self::assertNotContains('reportUnknownFields', $endpoints);
        self::assertNotContains('__construct', $endpoints);
    }

    public function testACallThatAnsweredIsRecordedAndItsResultHandedBack(): void
    {
        $sweep = new Sweep();

        $result = self::quietly(static fn () => $sweep->run('accountStatus', static fn (): string => 'answered'));

        self::assertSame('answered', $result);
        self::assertStringContainsString('1 of ', $sweep->report());
    }

    /**
     * One endpoint refusing must not end the sweep: an artist with no
     * donations answers with an error, and there are another hundred
     * endpoints after it.
     */
    public function testARefusalIsRecordedRatherThanThrown(): void
    {
        $sweep = new Sweep();

        $result = self::quietly(static fn () => $sweep->run(
            'artistsDonation',
            static fn () => throw new NotFoundException('Unknown HTTP error (404)'),
        ));

        $report = $sweep->report();

        self::assertNull($result);
        self::assertStringContainsString('1 answered with an error', $report);
        self::assertStringContainsString('artistsDonation', $report);
        self::assertStringContainsString('Unknown HTTP error (404)', $report);
    }

    public function testEndpointsLeftAloneAreGroupedByReason(): void
    {
        $sweep = new Sweep();
        $sweep->skipAll(['pinAlbum' => 'writes', 'unpinAlbum' => 'writes', 'revokeToken' => 'auth']);

        $report = $sweep->report();

        self::assertStringContainsString('left alone on purpose', $report);
        self::assertMatchesRegularExpression('/writes\s+pinAlbum, unpinAlbum/', $report);
        self::assertMatchesRegularExpression('/auth\s+revokeToken/', $report);
    }

    /**
     * The whole point: an endpoint nobody thought about is named.
     */
    public function testAnEndpointNeitherCalledNorSkippedIsNamed(): void
    {
        $sweep = new Sweep();

        $report = $sweep->report();

        self::assertStringContainsString('not covered', $report);
        self::assertStringContainsString('accountStatus', $report);
    }

    public function testACompleteSweepSaysSo(): void
    {
        $sweep = new Sweep();

        foreach (Sweep::endpoints() as $endpoint) {
            $sweep->skip($endpoint, 'writes');
        }

        self::assertStringContainsString('every endpoint is either called or deliberately skipped', $sweep->report());
    }

    /**
     * A name that is not a method of the client is a typo in the sweep, and a
     * typo would otherwise show up as an endpoint that is never covered while
     * looking, in the sweep's own source, as though it were.
     */
    public function testAMisspelledEndpointIsCalledOut(): void
    {
        $sweep = new Sweep();
        $sweep->skip('accuontStatus', 'writes');

        $report = $sweep->report();

        self::assertStringContainsString('a typo in this sweep', $report);
        self::assertStringContainsString('accuontStatus', $report);
    }

    /**
     * The collector is told which call is running, so an unplaceable field is
     * reported with the endpoint it arrived on.
     */
    public function testTheCollectorIsToldWhichCallIsRunning(): void
    {
        $collector = new UnknownFieldCollector();
        $sweep = new Sweep($collector);

        self::quietly(static fn () => $sweep->run('albumsWithTracks', static function () use ($collector): string {
            // Stands in for a model reporting mid-response.
            $collector->log('warning', 'unknown', [
                'model' => 'App\\Model\\Album',
                'fields' => ['albumType'],
                'types' => ['albumType' => 'string'],
            ]);

            return 'answered';
        }));

        self::assertStringContainsString('seen on albumsWithTracks', $collector->summary());
    }

    /**
     * Once the call is over, anything else reported must not be pinned on it.
     */
    public function testTheCallIsForgottenAfterwards(): void
    {
        $collector = new UnknownFieldCollector();
        $sweep = new Sweep($collector);

        self::quietly(static fn () => $sweep->run('album', static fn (): string => 'answered'));

        $collector->log('warning', 'unknown', [
            'model' => 'App\\Model\\Album',
            'fields' => ['albumType'],
            'types' => ['albumType' => 'string'],
        ]);

        self::assertStringNotContainsString('seen on', $collector->summary());
    }

    /**
     * The sweep prints as it goes, which is right when a person is watching a
     * hundred calls go by and wrong inside a test.
     *
     * @template T
     *
     * @param callable(): T $act
     *
     * @return T
     */
    private static function quietly(callable $act): mixed
    {
        ob_start();

        try {
            return $act();
        } finally {
            ob_end_clean();
        }
    }
}
