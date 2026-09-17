<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\MusicHistory;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\Track;

/**
 * One entry of the history: a type, what it points at, and optionally the
 * thing itself.
 */
final class MusicHistoryItem extends Model
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?MusicHistoryItemData $data = null,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * The track this entry is, when it is one.
     */
    public function track(): ?Track
    {
        $model = $this->data?->fullModel;

        return $model instanceof Track ? $model : null;
    }

    /**
     * What was being listened to, when this entry is a context rather than a
     * track.
     */
    public function context(): ?MusicHistoryContextFullModel
    {
        $model = $this->data?->fullModel;

        return $model instanceof MusicHistoryContextFullModel ? $model : null;
    }

    /**
     * The filled-in model is a track for a track and a context for an album,
     * an artist, a playlist or a station — and only this entry's type says
     * which, so the data is assembled here rather than in the data model.
     */
    protected static function prepare(array $args, array $data, ?Client $client): array
    {
        $raw = $data['data'] ?? null;

        if (!is_array($raw)) {
            $args['data'] = null;

            return $args;
        }

        $full = $raw['fullModel'] ?? null;
        unset($raw['fullModel']);

        $args['data'] = MusicHistoryItemData::fromApi($raw, $client);

        if (null === $args['data'] || !is_array($full)) {
            return $args;
        }

        $model = 'track' === ($data['type'] ?? null)
            ? Track::fromApi($full, $client)
            : MusicHistoryContextFullModel::fromApi($full, $client);

        $args['data'] = new MusicHistoryItemData($args['data']->itemId, $model, $client);

        return $args;
    }

    protected function identity(): array
    {
        return [$this->type, $this->data];
    }
}
