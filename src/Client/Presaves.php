<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Model\Presave\Presaves as PresavedAlbums;

/**
 * Presaves — albums the account asks to be told about before they are out.
 */
trait Presaves
{
    /**
     * What the account has presaved.
     */
    public function usersPresaves(
        string|int|null $userId = null,
        bool $includeReleased = true,
        bool $includeUpcoming = true,
    ): ?PresavedAlbums {
        $userId ??= $this->accountUid();

        $result = $this->request->get($this->getBaseUrl().'/users/'.$userId.'/presaves', [
            'includeReleased' => self::presaveFlag($includeReleased),
            'includeUpcoming' => self::presaveFlag($includeUpcoming),
        ]);

        return PresavedAlbums::fromApi($result, $this);
    }

    /**
     * Ask to be told about an album when it comes out.
     *
     * @param bool $likeAfterRelease also like it once it is released
     */
    public function usersPresavesAdd(
        string|int $albumId,
        bool $likeAfterRelease = true,
        string|int|null $userId = null,
    ): bool {
        $userId ??= $this->accountUid();

        $result = $this->request->post($this->getBaseUrl().'/users/'.$userId.'/presaves/add', [
            'albumId' => $albumId,
            'likeAfterRelease' => self::presaveFlag($likeAfterRelease),
        ]);

        return 'ok' === $result;
    }

    public function usersPresavesRemove(string|int $albumId, string|int|null $userId = null): bool
    {
        $userId ??= $this->accountUid();

        $result = $this->request->post($this->getBaseUrl().'/users/'.$userId.'/presaves/remove', [
            'albumId' => $albumId,
        ]);

        return 'ok' === $result;
    }

    /**
     * Lowercase, unlike every other boolean this library sends.
     *
     * The account, search and likes endpoints take `True` and `False`
     * capitalized; these take them lowercased. That is the API's
     * inconsistency, not ours.
     */
    private static function presaveFlag(bool $value): string
    {
        return $value ? 'true' : 'false';
    }
}
