<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Client;

use LuckyWins\YandexMusic\Exception\BadRequestException;
use LuckyWins\YandexMusic\Exception\DeviceAuthException;
use LuckyWins\YandexMusic\Exception\UnauthorizedException;
use LuckyWins\YandexMusic\Model\DeviceAuth\DeviceCode;
use LuckyWins\YandexMusic\Model\DeviceAuth\OAuthToken;

/**
 * The OAuth device flow — the only way left to obtain a token.
 *
 * The password grant this library used until now was withdrawn by Yandex. In
 * its place, the caller asks for a device code, shows it to the user, and polls
 * until the user has confirmed it in a browser.
 *
 * The credentials below are the ones the reference library uses, which describes
 * them as belonging to an official Yandex.Music client. That attribution is not
 * independently verified here — what is verified is that they work. Yandex does
 * not let anyone register their own music OAuth app, so there is no alternative
 * to using them.
 */
trait DeviceAuth
{
    private const OAUTH_BASE_URL = 'https://oauth.yandex.ru';

    private const DEFAULT_CLIENT_ID = '23cabbbdc6cd418abb4b39c32c41195d';

    private const DEFAULT_CLIENT_SECRET = '53bc75238f0c4d08a118e51fe9203300';

    private const DEFAULT_DEVICE_NAME = 'YandexMusicAPI';

    /**
     * Per RFC 8628, a `slow_down` response means back off by five seconds.
     */
    private const SLOW_DOWN_INCREMENT = 5.0;

    /**
     * Step one: ask for a code for the user to confirm.
     */
    public function requestDeviceCode(
        ?string $deviceId = null,
        ?string $deviceName = null,
        ?string $clientId = null,
    ): DeviceCode {
        $result = $this->request->post(self::OAUTH_BASE_URL.'/device/code', [
            'client_id' => $clientId ?? self::DEFAULT_CLIENT_ID,
            'device_id' => $deviceId ?? self::randomDeviceId(),
            'device_name' => $deviceName ?? self::DEFAULT_DEVICE_NAME,
        ]);

        $code = DeviceCode::fromApi($result, $this);

        if (null === $code) {
            throw new DeviceAuthException('Could not read the device code response');
        }

        return $code;
    }

    /**
     * Step two, asked repeatedly: has the user confirmed yet?
     *
     * Returns null while the user has not answered — that is the normal state
     * for most of the flow, not a failure. Everything else raises, including a
     * `slow_down`, which deviceAuth() handles by widening the interval.
     */
    public function pollDeviceToken(
        string $deviceCode,
        ?string $clientId = null,
        ?string $clientSecret = null,
    ): ?OAuthToken {
        try {
            $result = $this->request->post(self::OAUTH_BASE_URL.'/token', [
                'grant_type' => 'device_code',
                'code' => $deviceCode,
                'client_id' => $clientId ?? self::DEFAULT_CLIENT_ID,
                'client_secret' => $clientSecret ?? self::DEFAULT_CLIENT_SECRET,
            ]);
        } catch (BadRequestException|UnauthorizedException $e) {
            // Compare the code the server sent, not the text of the message:
            // descriptions are free-form and get reworded.
            if ('authorization_pending' === $e->getErrorCode()) {
                return null;
            }

            throw new DeviceAuthException($e->getMessage(), $e->getErrorCode(), $e);
        }

        return OAuthToken::fromApi($result, $this);
    }

    /**
     * The whole flow: request a code, hand it to the caller to display, then
     * poll until the user confirms.
     *
     * On success the token is applied to this client, so subsequent calls are
     * authorized. Persisting it is the caller's business — this library never
     * writes it anywhere and never refreshes it.
     *
     * @param callable(DeviceCode): void $onCode       shows the code to the user
     * @param float|null                 $pollInterval overrides the server's suggested interval
     * @param float|null                 $timeout      overrides the code's own lifetime
     * @param (callable(): bool)|null    $shouldCancel polled each round; true aborts
     *
     * @throws DeviceAuthException on cancellation, timeout, or a refusal
     */
    public function deviceAuth(
        callable $onCode,
        ?float $pollInterval = null,
        ?float $timeout = null,
        ?callable $shouldCancel = null,
        ?string $deviceId = null,
        ?string $deviceName = null,
        ?string $clientId = null,
        ?string $clientSecret = null,
    ): OAuthToken {
        $code = $this->requestDeviceCode($deviceId, $deviceName, $clientId);

        $onCode($code);

        $interval = $pollInterval ?? (float) $code->interval;
        $total = $timeout ?? (float) $code->expiresIn;
        $deadline = $this->clock->now() + $total;

        while (true) {
            if (null !== $shouldCancel && $shouldCancel()) {
                throw new DeviceAuthException('Device authorization was cancelled by the caller');
            }

            try {
                $token = $this->pollDeviceToken($code->deviceCode, $clientId, $clientSecret);
            } catch (DeviceAuthException $e) {
                if ('slow_down' !== $e->getErrorCode()) {
                    throw $e;
                }

                $interval += self::SLOW_DOWN_INCREMENT;
                $token = null;
            }

            if (null !== $token) {
                $this->setToken($token->accessToken);

                return $token;
            }

            if ($this->clock->now() >= $deadline) {
                throw new DeviceAuthException(
                    sprintf('Timed out after %ss waiting for the user to confirm', $total),
                );
            }

            $this->clock->sleep($interval);
        }
    }

    /**
     * Revoke a token, so that it stops working immediately.
     *
     * Use this the moment a token is exposed — pasted into a chat, committed,
     * written to a log. Ending the session in Yandex ID is not the same thing
     * and leaves the token working.
     *
     * Beware the endpoint's answer: it reports success for a token that never
     * existed, so it proves nothing on its own. Confirm by making a request and
     * seeing it rejected: any authorized call should now raise
     * UnauthorizedException. See examples/revoke_token.php.
     *
     * Revoking this client's own token also clears it here, leaving the client
     * unauthorized rather than holding a credential that no longer works.
     *
     * @param string|null $token the token to revoke; defaults to this client's own
     *
     * @throws DeviceAuthException when there is no token to revoke
     */
    public function revokeToken(
        ?string $token = null,
        ?string $clientId = null,
        ?string $clientSecret = null,
    ): void {
        $own = null === $token;
        $token ??= $this->getToken();

        if (null === $token) {
            throw new DeviceAuthException('This client holds no token, and none was given to revoke');
        }

        $this->request->post(self::OAUTH_BASE_URL.'/revoke_token', [
            'access_token' => $token,
            'client_id' => $clientId ?? self::DEFAULT_CLIENT_ID,
            'client_secret' => $clientSecret ?? self::DEFAULT_CLIENT_SECRET,
        ]);

        if ($own) {
            $this->forgetToken();
        }
    }

    /**
     * Ten characters of [A-Za-z0-9], matching what the reference library sends.
     */
    private static function randomDeviceId(): string
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $id = '';

        for ($i = 0; $i < 10; ++$i) {
            $id .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $id;
    }
}
