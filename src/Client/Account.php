<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\Account\Status;
use LuckyWins\YandexMusic\Model\Account\UserSettings;
use LuckyWins\YandexMusic\Model\Experiment\ExperimentsDetails;
use LuckyWins\YandexMusic\Model\PermissionAlerts;
use LuckyWins\YandexMusic\Model\PromoCodeStatus;
use LuckyWins\YandexMusic\Model\Settings;

/**
 * The account behind the token: who it is, what it may do, what it pays for.
 *
 * Note that two of these endpoints sit at the root rather than under /account —
 * `settings` and `permission-alerts` — which is easy to get wrong when adding
 * methods here by copying a neighbour.
 */
trait Account
{
    private ?Status $me = null;

    private ?int $accountUid = null;

    /**
     * Load the account for the current token.
     *
     * Not called for you: constructing a client performs no requests. Call it
     * once before anything that acts on behalf of the user, which needs the
     * account id this fetches.
     */
    public function init(): self
    {
        $this->me = $this->accountStatus();
        $this->accountUid = $this->me?->account?->uid;

        return $this;
    }

    /**
     * The account status loaded by init(), or null if it has not run.
     */
    public function me(): ?Status
    {
        return $this->me;
    }

    /**
     * The user id every per-user endpoint needs. Filled in by init().
     */
    public function getAccountUid(): ?int
    {
        return $this->accountUid;
    }

    public function accountStatus(): ?Status
    {
        return Status::fromApi($this->request->get($this->getBaseUrl().'/account/status'), $this);
    }

    public function accountSettings(): ?UserSettings
    {
        return UserSettings::fromApi($this->request->get($this->getBaseUrl().'/account/settings'), $this);
    }

    /**
     * Change one setting.
     *
     * The parameter names are the property names of UserSettings. Yandex
     * validates nothing and silently ignores what it does not recognize, so a
     * typo looks exactly like success — read the result back to be sure.
     */
    public function accountSettingsSet(string $param, string|int|bool $value): ?UserSettings
    {
        return $this->accountSettingsSetMany([$param => $value]);
    }

    /**
     * Change several settings at once.
     *
     * @param array<string, string|int|bool> $data
     */
    public function accountSettingsSetMany(array $data): ?UserSettings
    {
        $body = array_map(self::settingValue(...), $data);

        return UserSettings::fromApi($this->request->post($this->getBaseUrl().'/account/settings', $body), $this);
    }

    /**
     * A user's playback settings.
     *
     * The same model as accountSettings(), read through the user rather than
     * through the account. The reference library files this among the
     * playlist methods; it belongs with the account.
     *
     * The service no longer serves it. `/users/{uid}/settings` answers
     * `not-found` whether the id is passed or taken from the account, while
     * `/account/settings` answers normally — checked on 2026-09-17, and the
     * reference is behind here too. Kept rather than deleted because it costs
     * nothing and the endpoint may come back; use accountSettings() instead.
     */
    public function usersSettings(string|int|null $userId = null): ?UserSettings
    {
        $userId ??= $this->accountUid();

        $result = $this->request->get($this->getBaseUrl().'/users/'.$userId.'/settings');

        return UserSettings::fromApi(is_array($result) ? ($result['userSettings'] ?? null) : null, $this);
    }

    /**
     * What the account can be sold, and where to buy it.
     */
    public function settings(): ?Settings
    {
        // Root path, not /account/settings — that is a different endpoint.
        return Settings::fromApi($this->request->get($this->getBaseUrl().'/settings'), $this);
    }

    public function permissionAlerts(): ?PermissionAlerts
    {
        return PermissionAlerts::fromApi($this->request->get($this->getBaseUrl().'/permission-alerts'), $this);
    }

    /**
     * The A/B experiments the account is in, as a bare map.
     *
     * No model: the payload has no schema, and the reference library's class
     * for it is an empty bag that copies whatever arrived into itself.
     *
     * @return array<string, mixed>
     */
    public function accountExperiments(): array
    {
        $result = $this->request->get($this->getBaseUrl().'/account/experiments');

        return is_array($result) ? $result : [];
    }

    public function accountExperimentsDetails(): ?ExperimentsDetails
    {
        $result = $this->request->get($this->getBaseUrl().'/account/experiments/details');

        return ExperimentsDetails::fromApi($result, $this);
    }

    /**
     * Redeem a promo code.
     *
     * @param string|null $language response language, ISO 639-1; the client's own by default
     */
    public function consumePromoCode(string $code, ?string $language = null): ?PromoCodeStatus
    {
        $result = $this->request->post($this->getBaseUrl().'/account/consume-promo-code', [
            'code' => $code,
            'language' => $language ?? $this->getLanguage(),
        ]);

        return PromoCodeStatus::fromApi($result, $this);
    }

    /**
     * The account id, or a clear failure when there is none.
     *
     * Lives here rather than with the endpoints that need it because it is
     * account state: playlists, likes and dislikes all act on behalf of a
     * user and all need this answer.
     */
    private function accountUid(): int
    {
        $uid = $this->getAccountUid();

        if (null === $uid) {
            throw new YandexMusicException(
                'No account is loaded. Call init() on an authorized client before using '
                .'endpoints that act on behalf of a user.',
            );
        }

        return $uid;
    }

    /**
     * Encode a setting value for the wire.
     *
     * Booleans go as `True`/`False`, capitalized. That is what the reference
     * library sends — it stringifies Python booleans — and it is the spelling
     * known to work. Lowercase is untested, so it is not worth guessing at.
     */
    private static function settingValue(string|int|bool $value): string
    {
        if (is_bool($value)) {
            return $value ? 'True' : 'False';
        }

        return (string) $value;
    }
}
