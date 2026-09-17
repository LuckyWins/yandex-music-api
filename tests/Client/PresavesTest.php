<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Presaves as PresavesTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Presave\Presaves;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PresavesTrait::class)]
final class PresavesTest extends TestCase
{
    private const UID = 1130000002804451;

    private const STATUS = [
        'result' => [
            'account' => [
                'now' => '2026-09-17T12:00:00+00:00',
                'serviceAvailable' => true,
                'uid' => self::UID,
                'login' => 'user@yandex.ru',
            ],
        ],
    ];

    /**
     * These flags go lowercase, where the account, search and likes
     * endpoints take them capitalized. The API is inconsistent; the library
     * follows it.
     */
    public function testTheFlagsAreLowercased(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => ['upcomingAlbums' => [['id' => 1, 'title' => 'Скоро']]]]);

        $presaves = $this->initialized($http)->usersPresaves(includeReleased: false);

        self::assertInstanceOf(Presaves::class, $presaves);
        self::assertCount(1, $presaves->upcomingAlbums);
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/presaves?includeReleased=false&includeUpcoming=true',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testAddingAndRemoving(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => 'ok'])
            ->queue(['result' => 'ok']);

        $client = $this->initialized($http);

        self::assertTrue($client->usersPresavesAdd(4243618, likeAfterRelease: false));
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/presaves/add',
            (string) $http->requestAt(1)->getUri(),
        );
        self::assertSame(
            ['albumId' => '4243618', 'likeAfterRelease' => 'false'],
            $http->formBodyAt(1),
        );

        self::assertTrue($client->usersPresavesRemove(4243618));
        self::assertSame(
            'https://api.music.yandex.net/users/'.self::UID.'/presaves/remove',
            (string) $http->requestAt(2)->getUri(),
        );
        self::assertSame(['albumId' => '4243618'], $http->formBodyAt(2));
    }

    public function testARefusedPresaveIsReported(): void
    {
        $http = (new MockHttpClient())
            ->queue(self::STATUS)
            ->queue(['result' => 'error']);

        self::assertFalse($this->initialized($http)->usersPresavesAdd(4243618));
    }

    public function testAnotherUsersPresavesCanBeRead(): void
    {
        $http = (new MockHttpClient())->queue(['result' => []]);

        $this->client($http)->usersPresaves(503646255);

        self::assertStringStartsWith(
            'https://api.music.yandex.net/users/503646255/presaves',
            (string) $http->lastRequest()->getUri(),
        );
    }

    private function initialized(MockHttpClient $http): Client
    {
        return $this->client($http)->init();
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
