<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Clip\ClipsWillLike;

/**
 * Clips — short videos attached to tracks.
 */
trait Clips
{
    /**
     * Fetch clips by id.
     *
     * @param string|int|list<string|int> $clipIds
     *
     * @return list<Clip>
     */
    public function clips(string|int|array $clipIds): array
    {
        $result = $this->request->get($this->getBaseUrl().'/clips', [
            'clipIds' => is_array($clipIds) ? implode(',', $clipIds) : $clipIds,
        ]);

        return Clip::listFromApi($result, $this);
    }

    /**
     * A page of clips the service thinks the account will like.
     */
    public function clipsWillLike(int $page = 0, int $pageSize = 20): ?ClipsWillLike
    {
        $result = $this->request->get($this->getBaseUrl().'/clips/will/like', [
            'page' => $page,
            'pageSize' => $pageSize,
        ]);

        return ClipsWillLike::fromApi($result, $this);
    }
}
