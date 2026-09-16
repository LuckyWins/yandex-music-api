<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Tracks as TracksTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Credits;
use LuckyWins\YandexMusic\Model\Shot\ShotEvent;
use LuckyWins\YandexMusic\Model\Supplement\Supplement;
use LuckyWins\YandexMusic\Model\Track\SimilarTracks;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Model\Track\TrackLyrics;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TracksTrait::class)]
final class TracksTest extends TestCase
{
    public function testTracksPostsTheIds(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [['id' => 1, 'title' => 'Нирвана']]]);

        $tracks = $this->client($http)->tracks([1, 2, 3]);

        self::assertCount(1, $tracks);
        self::assertInstanceOf(Track::class, $tracks[0]);
        self::assertSame('POST', $http->lastRequest()->getMethod());
        self::assertSame('https://api.music.yandex.net/tracks', (string) $http->lastRequest()->getUri());
        self::assertSame(['track-ids' => '1,2,3', 'with-positions' => 'True'], $http->formBodyAt(0));
    }

    public function testDownloadInfoIsTyped(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            ['codec' => 'mp3', 'bitrateInKbps' => 320, 'gain' => false, 'preview' => false,
                'downloadInfoUrl' => 'https://storage.invalid/x', 'direct' => false],
        ]]);

        $infos = $this->client($http)->tracksDownloadInfo(31190260);

        self::assertCount(1, $infos);
        self::assertSame(320, $infos[0]->bitrateInKbps);
        self::assertSame(
            'https://api.music.yandex.net/tracks/31190260/download-info',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * The signature is what makes this endpoint answer at all; a wrong one is
     * refused rather than ignored.
     */
    public function testLyricsRequestIsSigned(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'downloadUrl' => 'https://music-lyrics.invalid/x',
            'lyricId' => 1,
            'externalLyricId' => '2',
            'writers' => [],
        ]]);

        $lyrics = $this->client($http)->tracksLyrics(4784420, 'LRC');

        self::assertInstanceOf(TrackLyrics::class, $lyrics);

        [$sign, $timestamp, $format] = $this->signedQuery($http->lastRequest()->getUri()->getQuery());

        self::assertSame('LRC', $format);

        // Recompute independently: the track id and the timestamp, joined with
        // nothing between them, signed with the key lifted from the app.
        self::assertSame(
            base64_encode(hash_hmac('sha256', '4784420'.$timestamp, 'p93jhgh689SBReK6ghtw62', true)),
            $sign,
        );
    }

    /**
     * The reference library's own test vector, so a divergence in the
     * algorithm shows up here rather than as a refused request later.
     */
    public function testSignatureMatchesTheReferenceVector(): void
    {
        self::assertSame(
            'vssEEweZhgv2Aud0rdH9maOXUC03ZkZ/hlo6bSRN8Qg=',
            base64_encode(hash_hmac('sha256', '4784420'.'1668687184', 'SUPER_SECRET_KEY', true)),
        );
    }

    /**
     * A composite id is reduced to its track part before signing.
     */
    public function testCompositeIdIsReducedBeforeSigning(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'downloadUrl' => 'u', 'lyricId' => 1, 'externalLyricId' => '2', 'writers' => [],
        ]]);

        $this->client($http)->tracksLyrics('4784420:37696396');

        [$sign, $timestamp] = $this->signedQuery($http->lastRequest()->getUri()->getQuery());

        self::assertSame(
            base64_encode(hash_hmac('sha256', '4784420'.$timestamp, 'p93jhgh689SBReK6ghtw62', true)),
            $sign,
        );
    }

    public function testSupplementAndSimilarAndCredits(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['id' => 1, 'description' => 'Описание']])
            ->queue(['result' => ['track' => ['id' => 1], 'similarTracks' => [['id' => 2]]]])
            ->queue(['result' => ['credits' => [['title' => 'Продюсер', 'value' => 'Кто-то']]]]);

        $client = $this->client($http);

        self::assertInstanceOf(Supplement::class, $client->trackSupplement(1));
        self::assertInstanceOf(SimilarTracks::class, $client->tracksSimilar(1));
        self::assertInstanceOf(Credits::class, $client->tracksCredits(1));

        self::assertSame('https://api.music.yandex.net/tracks/1/supplement', (string) $http->requestAt(0)->getUri());
        self::assertSame('https://api.music.yandex.net/tracks/1/similar', (string) $http->requestAt(1)->getUri());
        self::assertSame('https://api.music.yandex.net/tracks/1/credits', (string) $http->requestAt(2)->getUri());
    }

    /**
     * Timestamps go out as real UTC. The reference stamps local time and
     * labels it Z, which is wrong anywhere but on a UTC machine.
     */
    public function testPlayAudioReportsInUtc(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'ok']);

        $reported = $this->client($http)->playAudio(1, 'test', 10, trackLengthSeconds: 273, totalPlayedSeconds: 273);

        self::assertTrue($reported);

        $body = $http->formBodyAt(0);
        self::assertSame('1', $body['track-id']);
        self::assertSame('10', $body['album-id']);
        self::assertSame('273', $body['total-played-seconds']);
        self::assertSame('False', $body['from-cache']);
        self::assertStringEndsWith('+00:00', $body['timestamp']);
        self::assertSame($body['timestamp'], $body['client-now']);
    }

    public function testPlayAudioReportsFailure(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'not ok']);

        self::assertFalse($this->client($http)->playAudio(1, 'test', 10));
    }

    /**
     * The event is wrapped a level down, alongside advertising this library
     * does not model.
     */
    public function testAfterTrackUnwrapsTheEvent(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'shotEvent' => ['eventId' => 'e1', 'shots' => [
                ['order' => 0, 'played' => false, 'shotId' => 'a', 'status' => 'ready'],
            ]],
        ]]);

        $event = $this->client($http)->afterTrack(2, '940441070:12345', prevTrackId: 1);

        self::assertInstanceOf(ShotEvent::class, $event);
        self::assertSame('e1', $event->eventId);
        self::assertCount(1, $event->shots);

        $query = $this->query($http->lastRequest()->getUri()->getQuery());
        self::assertSame('2', $query['nextTrackId'] ?? null);
        self::assertSame('1', $query['prevTrackId'] ?? null);
        self::assertSame('940441070:12345', $query['contextItem'] ?? null);
    }

    public function testAfterTrackWithoutAnEvent(): void
    {
        $http = (new MockHttpClient())->queue(['result' => []]);

        self::assertNull($this->client($http)->afterTrack(2, '1:2'));
    }

    /**
     * The signature, timestamp and format from a signed request's query.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    private function signedQuery(string $raw): array
    {
        $query = $this->query($raw);

        $sign = $query['sign'] ?? null;
        $timestamp = $query['timeStamp'] ?? null;
        $format = $query['format'] ?? null;

        self::assertIsString($sign);
        self::assertIsString($timestamp);
        self::assertIsString($format);

        return [$sign, $timestamp, $format];
    }

    /** @return array<string, string> */
    private function query(string $raw): array
    {
        parse_str($raw, $parsed);

        $query = [];

        foreach ($parsed as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $query[$key] = $value;
            }
        }

        return $query;
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
