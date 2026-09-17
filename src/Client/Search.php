<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Exception\BadRequestException;
use LuckyWins\YandexMusic\Model\Search\Search as SearchResults;
use LuckyWins\YandexMusic\Model\Search\SearchType;
use LuckyWins\YandexMusic\Model\Search\Suggestions;

/**
 * Search.
 */
trait Search
{
    /**
     * Search for something.
     *
     * @param bool $noCorrect       search for exactly what was typed, rather than for
     *                              what the service thinks was meant
     * @param bool $playlistInBest  let a playlist be the single best match
     */
    public function search(
        string $text,
        bool $noCorrect = false,
        SearchType $type = SearchType::All,
        int $page = 0,
        bool $playlistInBest = true,
    ): ?SearchResults {
        $result = $this->request->get($this->getBaseUrl().'/search', [
            'text' => $text,
            // Capitalized on purpose: this is what the reference library sends
            // and what the endpoint is known to accept.
            'nocorrect' => $noCorrect ? 'True' : 'False',
            'type' => $type->value,
            'page' => $page,
            'playlist-in-best' => $playlistInBest ? 'True' : 'False',
        ]);

        if (is_string($result)) {
            // A rejected query comes back as prose with a 200, which would
            // otherwise deserialize to nothing at all and look like no matches.
            throw new BadRequestException($result);
        }

        return SearchResults::fromApi($result, $this);
    }

    /**
     * What to offer for a partly typed query.
     */
    public function searchSuggest(string $part): ?Suggestions
    {
        return Suggestions::fromApi(
            $this->request->get($this->getBaseUrl().'/search/suggest', ['part' => $part]),
            $this,
        );
    }
}
