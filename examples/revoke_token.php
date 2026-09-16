<?php

declare(strict_types=1);

/**
 * Revoke the stored token and prove it is dead.
 *
 * Run this the moment a token is exposed — pasted into a chat, committed to a
 * repository, printed into a log that someone else can read. A token is valid
 * for about a year and grants full access to the account, so there is no
 * waiting it out.
 *
 * Ending the session in Yandex ID under "Devices and sessions" does NOT do this.
 * That list is about sign-ins; the token keeps working afterwards.
 *
 * Asks for confirmation before doing anything. Pass --yes to skip the prompt,
 * which is also required when stdin is not a terminal, so that running this from
 * a script or a test never destroys a working token by accident.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Bootstrap;
use LuckyWins\YandexMusic\Exception\UnauthorizedException;
use LuckyWins\YandexMusic\Exception\YandexMusicException;

$token = Bootstrap::token();

if (null === $token) {
    echo "No token is stored, so there is nothing to revoke.\n";
    echo 'To revoke one held elsewhere, put it in '.Bootstrap::envPath()." first.\n";

    exit(0);
}

// Revocation is immediate and cannot be undone, so never do it on the strength
// of the script having been started. Anything automated has to say so out loud.
$confirmed = in_array('--yes', $argv, true);

if (!$confirmed) {
    if (!stream_isatty(STDIN)) {
        echo "Refusing to revoke without confirmation.\n";
        echo "Re-run with --yes if you really mean it.\n";

        exit(1);
    }

    echo 'About to revoke the token stored in '.Bootstrap::envPath().".\n";
    echo "This cannot be undone; you will have to authorize again.\n";
    echo 'Type "revoke" to continue: ';

    $answer = fgets(STDIN);

    if (!is_string($answer) || 'revoke' !== trim($answer)) {
        echo "Cancelled. Nothing was revoked.\n";

        exit(0);
    }
}

try {
    Bootstrap::client()->revokeToken($token);
} catch (YandexMusicException $e) {
    echo "The revocation request failed: {$e->getMessage()}\n";
    echo "The token is probably still valid. Try again.\n";

    exit(1);
}

echo "Revocation requested.\n";

// That request reports success even for a token that never existed, so it
// proves nothing. The only real evidence is the API refusing the token.
try {
    Bootstrap::client($token)->accountStatus();

    echo "\nWARNING: the token still works. It was NOT revoked.\n";
    echo "Do not assume it is safe. Try again, and if it keeps working, change\n";
    echo "the account password instead.\n";

    exit(1);
} catch (UnauthorizedException) {
    echo "Confirmed: the API now rejects it.\n";
} catch (YandexMusicException $e) {
    echo "\nCould not confirm — the check itself failed: {$e->getMessage()}\n";
    echo "Re-run this script to verify before assuming the token is dead.\n";

    exit(1);
}

Bootstrap::forgetToken();

echo 'Removed from '.Bootstrap::envPath().".\n";
echo "Run `php examples/device_auth.php` to authorize again.\n";
