<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Search as SearchTrait;
use LuckyWins\YandexMusic\Exception\BadRequestException;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Search\Search;
use LuckyWins\YandexMusic\Model\Search\SearchType;
use LuckyWins\YandexMusic\Model\Search\Suggestions;
use LuckyWins\YandexMusic\Model\Track\Track;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchTrait::class)]
final class SearchTest extends TestCase
{
    public function testSearchingForEverything(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'text' => 'нирвана',
            'type' => 'all',
            'tracks' => ['type' => 'track', 'total' => 1, 'results' => [['id' => 31190260, 'title' => 'Нирвана']]],
        ]]);

        $search = $this->client($http)->search('нирвана');

        self::assertInstanceOf(Search::class, $search);
        self::assertInstanceOf(Track::class, $search->tracks?->results[0]);
        self::assertSame('GET', $http->lastRequest()->getMethod());

        $query = $this->query($http);

        self::assertSame('нирвана', $query['text'] ?? null);
        self::assertSame('all', $query['type'] ?? null);
        self::assertSame('0', $query['page'] ?? null);
    }

    /**
     * The type is an enum here and a string on the wire; this is where the
     * two meet.
     */
    public function testTheTypeTravelsAsItsWireValue(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['text' => 'нирвана']]);

        $this->client($http)->search('нирвана', type: SearchType::PodcastEpisode);

        self::assertSame('podcast_episode', $this->query($http)['type'] ?? null);
    }

    /**
     * Both flags go capitalized, which is what the endpoint is known to
     * accept.
     */
    public function testTheFlagsAreCapitalized(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['text' => 'нирвана']]);

        $this->client($http)->search('нирвана', noCorrect: true, page: 3, playlistInBest: false);

        $query = $this->query($http);

        self::assertSame('True', $query['nocorrect'] ?? null);
        self::assertSame('False', $query['playlist-in-best'] ?? null);
        self::assertSame('3', $query['page'] ?? null);
    }

    public function testTheDefaultFlags(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['text' => 'нирвана']]);

        $this->client($http)->search('нирвана');

        $query = $this->query($http);

        self::assertSame('False', $query['nocorrect'] ?? null);
        self::assertSame('True', $query['playlist-in-best'] ?? null);
    }

    /**
     * A rejected query comes back as prose with a 200. Deserializing that
     * would produce an empty search, which reads as "nothing found" rather
     * than "the request was wrong".
     */
    public function testProseInsteadOfAResultIsAnError(): void
    {
        $http = (new MockHttpClient())->queue(['result' => 'invalid search type']);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('invalid search type');

        $this->client($http)->search('нирвана');
    }

    public function testSuggestionsAreTyped(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'best' => ['type' => 'artist', 'result' => ['id' => 4611844, 'name' => 'Miyagi']],
            'suggestions' => ['нирвана', 'нирвана miyagi'],
        ]]);

        $suggestions = $this->client($http)->searchSuggest('нирв');

        self::assertInstanceOf(Suggestions::class, $suggestions);
        self::assertCount(2, $suggestions->suggestions);
        self::assertSame('artist', $suggestions->best?->type);
        self::assertSame(
            'https://api.music.yandex.net/search/suggest?part=%D0%BD%D0%B8%D1%80%D0%B2',
            (string) $http->lastRequest()->getUri(),
        );
    }

    /**
     * @return array<string, string>
     */
    private function query(MockHttpClient $http): array
    {
        parse_str($http->lastRequest()->getUri()->getQuery(), $query);

        $result = [];

        foreach ($query as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function client(MockHttpClient $http): Client
    {
        return new Client('y0_token', new Request($http));
    }
}
