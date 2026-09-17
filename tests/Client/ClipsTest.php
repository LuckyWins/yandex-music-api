<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Clips as ClipsTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Clip\ClipsWillLike;
use LuckyWins\YandexMusic\Model\Credits;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ClipsTrait::class)]
final class ClipsTest extends TestCase
{
    public function testClipsAreFetchedByIdInTheQuery(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            ['clipId' => 91, 'title' => 'Нирвана'],
            ['clipId' => 92],
        ]]);

        $clips = $this->client($http)->clips([91, 92]);

        self::assertCount(2, $clips);
        self::assertInstanceOf(Clip::class, $clips[0]);
        self::assertSame('Нирвана', $clips[0]->title);
        self::assertSame('GET', $http->lastRequest()->getMethod());
        self::assertSame(
            'https://api.music.yandex.net/clips?clipIds=91%2C92',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testOneClipNeedsNoArray(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [['clipId' => 91]]]);

        self::assertCount(1, $this->client($http)->clips(91));
        self::assertSame('clipIds=91', $http->lastRequest()->getUri()->getQuery());
    }

    public function testSuggestedClipsArePaged(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'clips' => [['clipId' => 91]],
            'pager' => ['total' => 100, 'page' => 1, 'perPage' => 10],
        ]]);

        $clips = $this->client($http)->clipsWillLike(1, 10);

        self::assertInstanceOf(ClipsWillLike::class, $clips);
        self::assertSame(100, $clips->pager?->total);
        self::assertSame(
            'https://api.music.yandex.net/clips/will/like?page=1&pageSize=10',
            (string) $http->lastRequest()->getUri(),
        );
    }


    public function testCreditsAndDisclaimersForAClip(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['credits' => []]])
            ->queue(['result' => []]);

        $client = $this->client($http);

        self::assertInstanceOf(Credits::class, $client->clipsCredits(91));
        self::assertSame('https://api.music.yandex.net/clips/91/credits', (string) $http->requestAt(0)->getUri());

        // A list, like every other disclaimer endpoint.
        self::assertSame([], $client->clipsDisclaimer(91));
        self::assertSame('https://api.music.yandex.net/clips/91/disclaimer', (string) $http->requestAt(1)->getUri());
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
