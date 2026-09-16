<?php

declare(strict_types=1);

/**
 * Obtain a token through the OAuth device flow and store it.
 *
 * Run it, open the URL it prints, enter the code, and confirm. The token is
 * written straight to .env.local — it is never printed, because anything that
 * reaches a terminal reaches scrollback, shell history and screenshots too.
 *
 * .env.local is git-ignored and written owner-only. Treat it like a password
 * file: the token grants full access to the account, including the profile,
 * the phone numbers on it and the subscription.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Bootstrap;
use LuckyWins\YandexMusic\Exception\DeviceAuthException;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\DeviceAuth\DeviceCode;

if (null !== Bootstrap::token()) {
    echo 'A token is already stored in '.Bootstrap::envPath().".\n";
    echo "Delete it first if you want to authorize again.\n";

    exit(0);
}

$client = Bootstrap::client();

try {
    $token = $client->deviceAuth(static function (DeviceCode $code): void {
        echo "Open {$code->verificationUrl} and enter the code: {$code->userCode}\n";
        echo "Waiting for confirmation (the code is good for {$code->expiresIn}s)...\n";
    });
} catch (DeviceAuthException $e) {
    echo "Authorization failed: {$e->getMessage()}\n";

    exit(1);
}

Bootstrap::saveToken($token->accessToken);

echo "\nToken saved to ".Bootstrap::envPath()." (not printed on purpose).\n";

if (null !== $token->expiresIn) {
    printf("It is good for %d days.\n", intdiv($token->expiresIn, 86400));
}

// The token is already applied to the client, so this call is authorized.
try {
    $account = $client->init()->getAccount();
    echo 'Signed in as: '.Bootstrap::text($account, 'login')."\n";
} catch (YandexMusicException $e) {
    echo "Token stored, but the account check failed: {$e->getMessage()}\n";

    exit(1);
}
