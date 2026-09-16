<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Account;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Model\Model;

/**
 * The account's own preferences, as set in the apps.
 *
 * Several of these no longer do anything on Yandex's side — `promosDisabled`,
 * `adsDisabled` and `rbtDisabled` are documented as dead, and the two
 * scrobbling flags look it. They are kept so a response deserializes whole,
 * not because setting them achieves anything.
 */
final class UserSettings extends Model
{
    public function __construct(
        public readonly int $uid,
        public readonly bool $lastFmScrobblingEnabled,
        public readonly bool $shuffleEnabled,
        public readonly int $volumePercents,
        public readonly string $modified,
        public readonly bool $facebookScrobblingEnabled,
        public readonly bool $addNewTrackOnPlaylistTop,
        /** `private` or `public`. */
        public readonly string $userMusicVisibility,
        /** `private` or `public`. */
        public readonly string $userSocialVisibility,
        public readonly bool $rbtDisabled,
        /** `white` or `black`. */
        public readonly string $theme,
        public readonly bool $promosDisabled,
        public readonly bool $autoPlayRadio,
        public readonly bool $syncQueueEnabled,
        public readonly ?bool $adsDisabled = null,
        public readonly ?bool $diskEnabled = null,
        public readonly ?bool $showDiskTracksInLibrary = null,
        /** Whether explicit content is blocked. */
        public readonly ?bool $explicitForbidden = null,
        public readonly ?bool $childModEnabled = null,
        public readonly ?bool $childModeChangedByUser = null,
        /** Whether the taste-picking wizard has been completed. */
        public readonly ?bool $wizardIsPassed = null,
        /** The colour the collection is tinted with, as a hue. */
        public readonly ?int $userCollectionHue = null,
        public readonly ?bool $aiContentReductionEnabled = null,
        public readonly ?Client $client = null,
    ) {
    }

    protected function identity(): array
    {
        return [
            $this->uid, $this->lastFmScrobblingEnabled, $this->shuffleEnabled, $this->volumePercents,
            $this->modified, $this->facebookScrobblingEnabled, $this->addNewTrackOnPlaylistTop,
            $this->userMusicVisibility, $this->userSocialVisibility, $this->rbtDisabled, $this->theme,
            $this->promosDisabled, $this->autoPlayRadio, $this->syncQueueEnabled, $this->adsDisabled,
            $this->diskEnabled, $this->showDiskTracksInLibrary,
        ];
    }
}
