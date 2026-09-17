<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Labels as LabelsTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Label\Label;
use LuckyWins\YandexMusic\Model\Label\LabelAlbums;
use LuckyWins\YandexMusic\Model\Label\LabelArtists;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LabelsTrait::class)]
final class LabelsTest extends TestCase
{
    public function testALabelsPage(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'id' => 1234,
            'name' => 'Hajime Records',
            'description' => 'Лейбл дуэта',
        ]]);

        $label = $this->client($http)->label(1234);

        self::assertInstanceOf(Label::class, $label);
        self::assertSame('Hajime Records', $label->name);
        self::assertSame('https://api.music.yandex.net/labels/1234', (string) $http->lastRequest()->getUri());
    }

    public function testTheReleasesWithSorting(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['albums' => [['id' => 4243617, 'title' => 'Hajime']]]])
            ->queue(['result' => ['albums' => []]]);

        $client = $this->client($http);

        $albums = $client->labelAlbums(1234, 1, 5, sortBy: 'year', sortOrder: 'desc');

        self::assertInstanceOf(LabelAlbums::class, $albums);
        self::assertCount(1, $albums->albums);
        self::assertSame(
            'page=1&pageSize=5&sortBy=year&sortOrder=desc',
            $http->requestAt(0)->getUri()->getQuery(),
        );

        $client->labelAlbums(1234);

        self::assertSame('page=0&pageSize=20', $http->requestAt(1)->getUri()->getQuery());
    }

    public function testTheArtistsSigned(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'artists' => [['id' => 4611844, 'name' => 'Miyagi & Эндшпиль']],
            'pager' => ['total' => 7, 'page' => 0, 'perPage' => 20],
        ]]);

        $artists = $this->client($http)->labelArtists(1234);

        self::assertInstanceOf(LabelArtists::class, $artists);
        self::assertSame(7, $artists->pager?->total);
        self::assertSame(
            'https://api.music.yandex.net/labels/1234/artists?page=0&pageSize=20',
            (string) $http->lastRequest()->getUri(),
        );
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
