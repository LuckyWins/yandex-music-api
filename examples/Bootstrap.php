<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Examples;

use GuzzleHttp\Client as Guzzle;
use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Http\Request;
use RuntimeException;

/**
 * Credential handling for the example scripts.
 *
 * Deliberately not part of the library: loading configuration is the consuming
 * application's job, and a client library that reads dotfiles behind your back
 * is a library that surprises you.
 *
 * The token lives in .env.local, which is git-ignored and written with owner-only
 * permissions. Nothing here ever prints it — a token that reaches a terminal
 * ends up in scrollback, shell history and screenshots.
 */
final class Bootstrap
{
    public const TOKEN_KEY = 'YANDEX_MUSIC_TOKEN';

    public const RESOLVE_KEY = 'YANDEX_MUSIC_RESOLVE';

    /**
     * The token, from the environment first and .env.local second.
     */
    public static function token(): ?string
    {
        return self::read(self::TOKEN_KEY);
    }

    /**
     * One variable, from the environment first and .env.local second, so a
     * one-off run can override the file without editing it.
     */
    public static function read(string $key): ?string
    {
        $fromEnv = getenv($key);

        if (is_string($fromEnv) && '' !== $fromEnv) {
            return $fromEnv;
        }

        return self::readFromFile($key);
    }

    /**
     * Store the token, replacing any previous one, and lock the file down to
     * the current user.
     */
    public static function saveToken(string $token): void
    {
        $path = self::envPath();
        $lines = [];

        if (is_file($path)) {
            $existing = file($path, FILE_IGNORE_NEW_LINES);

            if (false !== $existing) {
                $lines = array_values(array_filter(
                    $existing,
                    static fn (string $line): bool => !str_starts_with(trim($line), self::TOKEN_KEY.'='),
                ));
            }
        }

        $lines[] = self::TOKEN_KEY.'='.$token;

        if (false === file_put_contents($path, implode("\n", $lines)."\n")) {
            throw new RuntimeException(sprintf('Could not write %s', $path));
        }

        // Owner read/write only. The file holds a year-long credential.
        chmod($path, 0o600);
    }

    /**
     * A client, with the address overrides applied if any are configured.
     */
    public static function client(?string $token = null): Client
    {
        return new Client($token, new Request(new Guzzle([
            'timeout' => 10,
            'http_errors' => false,
            'curl' => self::curlOptions(),
        ])));
    }

    /**
     * A client carrying the stored token, or an explanation of how to get one.
     */
    public static function authorizedClient(): Client
    {
        $token = self::token();

        if (null === $token) {
            throw new RuntimeException(
                'No token found. Run `php examples/device_auth.php` first, or set '
                .self::TOKEN_KEY.' in the environment.',
            );
        }

        return self::client($token);
    }

    /**
     * Address overrides for networks where DNS will not lead anywhere useful.
     *
     * Some networks block Yandex over IPv4 and the resolver hands out nothing
     * else, which looks exactly like the API being down. Setting RESOLVE_KEY to
     * a comma-separated list of curl resolve entries routes around it:
     *
     *     YANDEX_MUSIC_RESOLVE=oauth.yandex.ru:443:[2a02:6b8::15e],api.music.yandex.net:443:[2a02:6b8::5:246]
     *
     * See the README section on unreachable networks for how to find the
     * current addresses.
     *
     * @return array<int, list<string>>
     */
    private static function curlOptions(): array
    {
        $resolve = self::read(self::RESOLVE_KEY);

        if (null === $resolve) {
            return [];
        }

        $entries = array_values(array_filter(
            array_map(trim(...), explode(',', $resolve)),
            static fn (string $entry): bool => '' !== $entry,
        ));

        return [] === $entries ? [] : [CURLOPT_RESOLVE => $entries];
    }

    /**
     * Drop the stored token from .env.local, leaving any other variables alone.
     */
    public static function forgetToken(): void
    {
        $path = self::envPath();

        if (!is_file($path)) {
            return;
        }

        $existing = file($path, FILE_IGNORE_NEW_LINES);

        if (false === $existing) {
            return;
        }

        $kept = array_values(array_filter(
            $existing,
            static fn (string $line): bool => !str_starts_with(trim($line), self::TOKEN_KEY.'='),
        ));

        file_put_contents($path, [] === $kept ? '' : implode("\n", $kept)."\n");
        chmod($path, 0600);
    }

    /**
     * Read one scalar field out of a raw legacy response.
     *
     * Those endpoints hand back untyped arrays, so every field is mixed until
     * the domain is converted to models. This narrows it for display.
     *
     * @param array<array-key, mixed>|null $data
     */
    public static function text(?array $data, string $key, string $fallback = 'unknown'): string
    {
        $value = $data[$key] ?? null;

        return is_scalar($value) ? (string) $value : $fallback;
    }

    public static function envPath(): string
    {
        return dirname(__DIR__).'/.env.local';
    }

    private static function readFromFile(string $key): ?string
    {
        $path = self::envPath();

        if (!is_file($path)) {
            return null;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (false === $lines) {
            return null;
        }

        foreach ($lines as $line) {
            $line = trim($line);

            if (str_starts_with($line, '#') || !str_starts_with($line, $key.'=')) {
                continue;
            }

            $value = trim(substr($line, strlen($key) + 1), " \t\"'");

            if ('' !== $value) {
                return $value;
            }
        }

        return null;
    }
}
