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
    $client = Bootstrap::authorizedClient()->init();
} catch (YandexMusicException $e) {
    echo "Request failed: {$e->getMessage()}\n";

    exit(1);
}

$account = $client->getAccount();

// Deliberately narrow: the account payload also carries the real name, birthday
// and the phone numbers on the Yandex ID, none of which belong on a terminal.
printf("login   %s\n", Bootstrap::text($account, 'login'));
printf("uid     %s\n", Bootstrap::text($account, 'uid'));
printf("region  %s\n", Bootstrap::text($account, 'region'));
