<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Radio as RadioTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Account\Status;
use LuckyWins\YandexMusic\Model\Rotor\Dashboard;
use LuckyWins\YandexMusic\Model\Rotor\Diversity;
use LuckyWins\YandexMusic\Model\Rotor\FeedbackType;
use LuckyWins\YandexMusic\Model\Rotor\Id;
use LuckyWins\YandexMusic\Model\Rotor\MoodEnergy;
use LuckyWins\YandexMusic\Model\Rotor\StationResult;
use LuckyWins\YandexMusic\Model\Rotor\StationTracksResult;
use LuckyWins\YandexMusic\Tests\Support\FrozenClock;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RadioTrait::class)]
final class RadioTest extends TestCase
{
    public function testAccountStatusIsTheAccountModel(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'account' => [
                'now' => '2026-09-17T12:00:00+00:00',
                'serviceAvailable' => true,
                'uid' => 1,
                'login' => 'user@yandex.ru',
            ],
            'skipsPerHour' => 6,
        ]]);

        $status = $this->client($http)->rotorAccountStatus();

        self::assertInstanceOf(Status::class, $status);
        self::assertSame(6, $status->skipsPerHour);
        self::assertSame(
            'https://api.music.yandex.net/rotor/account/status',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testDashboardIsTyped(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'dashboardId' => 'd1',
            'stations' => [['station' => ['id' => ['type' => 'user', 'tag' => 'onyourwave'], 'name' => 'Моя волна']]],
        ]]);

        $dashboard = $this->client($http)->rotorStationsDashboard();

        self::assertInstanceOf(Dashboard::class, $dashboard);
        self::assertCount(1, $dashboard->stations);
        self::assertSame(
            'https://api.music.yandex.net/rotor/stations/dashboard',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * No language means the client's own, so the parameter has to be left out
     * rather than sent empty.
     */
    public function testTheStationListOnlyAsksForALanguageWhenGivenOne(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => [['station' => ['name' => 'Рок']]]])
            ->queue(['result' => []]);

        $client = $this->client($http);

        $stations = $client->rotorStationsList();

        self::assertCount(1, $stations);
        self::assertInstanceOf(StationResult::class, $stations[0]);
        self::assertSame('https://api.music.yandex.net/rotor/stations/list', (string) $http->requestAt(0)->getUri());

        $client->rotorStationsList('ru');

        self::assertSame(
            'https://api.music.yandex.net/rotor/stations/list?language=ru',
            (string) $http->requestAt(1)->getUri(),
        );
    }

    /**
     * A station is named by kind and tag, and the Id model the responses
     * carry has to be usable as that name.
     */
    public function testAStationCanBeNamedByItsId(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => [['station' => ['name' => 'Рок']]]])
            ->queue(['result' => [['station' => ['name' => 'Рок']]]]);

        $client = $this->client($http);

        $client->rotorStationInfo('genre:allrock');
        $client->rotorStationInfo(new Id('genre', 'allrock'));

        self::assertSame(
            'https://api.music.yandex.net/rotor/station/genre:allrock/info',
            (string) $http->requestAt(0)->getUri(),
        );
        self::assertSame(
            (string) $http->requestAt(0)->getUri(),
            (string) $http->requestAt(1)->getUri(),
        );
    }

    public function testTracksAskForTheSettingsByDefault(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'batchId' => 'b1',
            'sequence' => [['type' => 'track', 'track' => ['id' => 31190260, 'title' => 'Нирвана']]],
        ]]);

        $batch = $this->client($http)->rotorStationTracks('genre:allrock');

        self::assertInstanceOf(StationTracksResult::class, $batch);
        self::assertCount(1, $batch->tracks());
        self::assertSame(
            'https://api.music.yandex.net/rotor/station/genre:allrock/tracks?settings2=True',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testTracksCanContinueFromTheLastOnePlayed(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['batchId' => 'b2']]);

        $this->client($http)->rotorStationTracks('genre:allrock', settings2: false, queue: '31190260');

        self::assertSame(
            'https://api.music.yandex.net/rotor/station/genre:allrock/tracks?queue=31190260',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * Settings go as JSON: a form body is refused outright here, which is
     * where the reference library's version stops working.
     */
    public function testSettingsAreSentAsJson(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'ok']);

        $sent = $this->client($http)->rotorStationSettings(
            'genre:allrock',
            moodEnergy: MoodEnergy::Active,
            diversity: Diversity::Favorite,
        );

        self::assertTrue($sent);
        self::assertSame('application/json', $http->lastRequest()->getHeaderLine('Content-Type'));
        self::assertSame(
            ['moodEnergy' => 'active', 'diversity' => 'favorite'],
            $http->jsonBodyAt(0),
        );
        self::assertSame(
            'https://api.music.yandex.net/rotor/station/genre:allrock/settings3',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * A value this library does not know must still be sendable, which is why
     * these parameters take strings as well as enums.
     */
    public function testSettingsTakeBareStringsToo(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'ok']);

        $this->client($http)->rotorStationSettings('genre:allrock', moodEnergy: 'nostalgic');

        self::assertSame(['moodEnergy' => 'nostalgic'], $http->jsonBodyAt(0));
    }

    public function testFeedbackIsSentAsJsonWithTheBatchInTheQuery(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'ok']);

        $sent = $this->client($http)->rotorStationFeedback(
            'genre:allrock',
            FeedbackType::TrackFinished,
            timestamp: 1789647430.5,
            from: 'mobile-radio-genre-allrock',
            trackId: 31190260,
            totalPlayedSeconds: 30.5,
            batchId: 'b1',
        );

        self::assertTrue($sent);
        self::assertSame('application/json', $http->lastRequest()->getHeaderLine('Content-Type'));
        self::assertSame(
            'https://api.music.yandex.net/rotor/station/genre:allrock/feedback?batch-id=b1',
            (string) $http->lastRequest()->getUri(),
        );
        self::assertSame([
            'type' => 'trackFinished',
            'timestamp' => 1789647430.5,
            'trackId' => 31190260,
            'from' => 'mobile-radio-genre-allrock',
            'totalPlayedSeconds' => 30.5,
        ], $http->jsonBodyAt(0));
    }

    /**
     * Only what was given travels: an absent track id or origin must not go
     * as null.
     */
    public function testAbsentFieldsAreLeftOut(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'ok']);

        $this->client($http)->rotorStationFeedback('genre:allrock', FeedbackType::RadioStarted, timestamp: 1.5);

        self::assertSame(['type' => 'radioStarted', 'timestamp' => 1.5], $http->jsonBodyAt(0));
        self::assertSame(
            'https://api.music.yandex.net/rotor/station/genre:allrock/feedback',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * Without a timestamp the client supplies one, which is why the clock is
     * injectable.
     */
    public function testTheTimestampDefaultsToNow(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'ok']);

        $client = new Client('y0_token', new Request($http), clock: new FrozenClock(1789647430.75));
        $client->rotorStationFeedback('genre:allrock', FeedbackType::Skip, trackId: 1, totalPlayedSeconds: 2);

        self::assertSame(1789647430.75, $http->jsonBodyAt(0)['timestamp'] ?? null);
    }

    public function testAnUnknownFeedbackTypeCanStillBeSent(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'ok']);

        $this->client($http)->rotorStationFeedback('genre:allrock', 'somethingNew', timestamp: 1.5);

        self::assertSame('somethingNew', $http->jsonBodyAt(0)['type'] ?? null);
    }

    public function testTheFourNamedEventsSendTheirOwnType(): void
    {
        foreach ([
            ['rotorStationFeedbackRadioStarted', ['genre:allrock', 'mobile-radio', 'b1'], 'radioStarted'],
            ['rotorStationFeedbackTrackStarted', ['genre:allrock', 31190260, 'b1'], 'trackStarted'],
            ['rotorStationFeedbackTrackFinished', ['genre:allrock', 31190260, 30.0, 'b1'], 'trackFinished'],
            ['rotorStationFeedbackSkip', ['genre:allrock', 31190260, 1.0, 'b1'], 'skip'],
        ] as [$method, $arguments, $type]) {
            $http = (new MockHttpClient())->queue(['result' => 'ok']);

            self::assertTrue($this->client($http)->{$method}(...$arguments), $method);
            self::assertSame($type, $http->jsonBodyAt(0)['type'] ?? null, $method);
            self::assertStringEndsWith('feedback?batch-id=b1', (string) $http->lastRequest()->getUri(), $method);
        }
    }

    public function testARefusedFeedbackIsReported(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'not ok']);

        self::assertFalse(
            $this->client($http)->rotorStationFeedback('genre:allrock', FeedbackType::Skip, timestamp: 1.5),
        );
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
