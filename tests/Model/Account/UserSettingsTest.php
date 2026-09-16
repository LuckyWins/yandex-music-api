<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Account;

use LuckyWins\YandexMusic\Model\Account\UserSettings;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UserSettings::class)]
final class UserSettingsTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return UserSettings::class;
    }

    protected static function fullPayload(): array
    {
        return self::requiredPayload() + [
            'adsDisabled' => false,
            'diskEnabled' => true,
            'showDiskTracksInLibrary' => false,
            'explicitForbidden' => false,
            'childModEnabled' => false,
            'childModeChangedByUser' => false,
            'wizardIsPassed' => true,
            'userCollectionHue' => 210,
            'aiContentReductionEnabled' => false,
        ];
    }

    protected static function requiredPayload(): array
    {
        return [
            'uid' => 1130000002804451,
            'lastFmScrobblingEnabled' => false,
            'shuffleEnabled' => true,
            'volumePercents' => 70,
            'modified' => '2026-09-16T12:00:00+00:00',
            'facebookScrobblingEnabled' => false,
            'addNewTrackOnPlaylistTop' => true,
            'userMusicVisibility' => 'public',
            'userSocialVisibility' => 'private',
            'rbtDisabled' => false,
            'theme' => 'black',
            'promosDisabled' => true,
            'autoPlayRadio' => true,
            'syncQueueEnabled' => true,
        ];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(UserSettings::class, $model);
        self::assertSame(1130000002804451, $model->uid);
        self::assertSame(70, $model->volumePercents);
        self::assertSame('black', $model->theme);
        self::assertSame('public', $model->userMusicVisibility);
        self::assertSame('private', $model->userSocialVisibility);
        self::assertTrue($model->shuffleEnabled);
        self::assertTrue($model->diskEnabled);
        self::assertFalse($model->showDiskTracksInLibrary);

        // Sent by the live API; the reference library models none of these.
        self::assertFalse($model->explicitForbidden);
        self::assertTrue($model->wizardIsPassed);
        self::assertSame(210, $model->userCollectionHue);
        self::assertFalse($model->aiContentReductionEnabled);
    }

    protected function equalityTriple(): array
    {
        $make = static fn (int $volume): UserSettings => new UserSettings(
            1,
            false,
            true,
            $volume,
            'm',
            false,
            true,
            'public',
            'public',
            false,
            'black',
            true,
            true,
            true,
        );

        return [$make(70), $make(70), $make(50)];
    }

    /**
     * The API is known to send this one capitalized differently in places, and
     * the raw spelling cannot be derived from the reference library's own
     * naming. Matching ignores case and separators precisely so that this
     * cannot quietly drop the field.
     */
    public function testScrobblingFlagIsFoundWhateverItsSpelling(): void
    {
        foreach (['lastFmScrobblingEnabled', 'lastFMScrobblingEnabled', 'last_fm_scrobbling_enabled'] as $spelling) {
            $payload = self::requiredPayload();
            unset($payload['lastFmScrobblingEnabled']);
            $payload[$spelling] = true;

            $model = UserSettings::fromApi($payload, self::client());

            self::assertInstanceOf(UserSettings::class, $model, $spelling);
            self::assertTrue($model->lastFmScrobblingEnabled, $spelling);
        }
    }
}
