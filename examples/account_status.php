<?php

declare(strict_types=1);

/**
 * A smoke check against the live API using the stored token.
 *
 * Run `php examples/device_auth.php` first to obtain one.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Bootstrap;
use LuckyWins\YandexMusic\Exception\YandexMusicException;

try {
    $status = Bootstrap::authorizedClient()->init()->me();
} catch (YandexMusicException $e) {
    echo "Request failed: {$e->getMessage()}\n";

    exit(1);
}

if (null === $status?->account) {
    echo "The API answered, but with no account — is the token anonymous?\n";

    exit(1);
}

// Deliberately narrow: the account also carries the real name, the birthday and
// the phone numbers on the Yandex ID, none of which belong on a terminal.
printf("login    %s\n", $status->account->login ?? 'unknown');
printf("uid      %d\n", $status->account->uid ?? 0);
printf("region   %d\n", $status->account->region ?? 0);
printf("child    %s\n", true === $status->account->child ? 'yes' : 'no');
printf("plus     %s\n", $status->plus?->hasPlus ? 'yes' : 'no');

if (null !== $status->permissions) {
    printf("can      %s\n", implode(', ', $status->permissions->values));
}
