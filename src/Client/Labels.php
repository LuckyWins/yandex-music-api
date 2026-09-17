<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Label\Label;
use LuckyWins\YandexMusic\Model\Label\LabelAlbums;
use LuckyWins\YandexMusic\Model\Label\LabelArtists;

/**
 * Labels — who released what.
 */
trait Labels
{
    /**
     * A label's page.
     */
    public function label(string|int $labelId): ?Label
    {
        return Label::fromApi($this->request->get($this->getBaseUrl().'/labels/'.$labelId), $this);
    }

    /**
     * A page of a label's releases.
     *
     * @param string|null $sortBy    `year`, and whatever else the service takes
     * @param string|null $sortOrder `asc` or `desc`
     */
    public function labelAlbums(
        string|int $labelId,
        int $page = 0,
        int $pageSize = 20,
        ?string $sortBy = null,
        ?string $sortOrder = null,
    ): ?LabelAlbums {
        $params = ['page' => $page, 'pageSize' => $pageSize];

        if (null !== $sortBy) {
            $params['sortBy'] = $sortBy;
        }

        if (null !== $sortOrder) {
            $params['sortOrder'] = $sortOrder;
        }

        return LabelAlbums::fromApi(
            $this->request->get($this->getBaseUrl().'/labels/'.$labelId.'/albums', $params),
            $this,
        );
    }

    /**
     * A page of the artists signed to a label.
     */
    public function labelArtists(string|int $labelId, int $page = 0, int $pageSize = 20): ?LabelArtists
    {
        return LabelArtists::fromApi(
            $this->request->get($this->getBaseUrl().'/labels/'.$labelId.'/artists', [
                'page' => $page,
                'pageSize' => $pageSize,
            ]),
            $this,
        );
    }
}
