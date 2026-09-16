<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\Model;

/**
 * One way a track can be fetched: a codec, a bitrate, and a manifest to
 * resolve into an actual URL.
 *
 * The manifest is short-lived — roughly a minute — so resolve and download
 * promptly rather than collecting these for later.
 */
final class DownloadInfo extends Model
{
    /** Lifted from the desktop client years ago; unchanged since. */
    private const LINK_SALT = 'XGRlBW9FXlekgbPrRHuSiA';

    public function __construct(
        /** `mp3` or `aac`. */
        public readonly string $codec,
        /** 64, 128, 192 or 320. */
        public readonly int $bitrateInKbps,
        public readonly bool $gain,
        public readonly bool $preview,
        /** The XML manifest that resolves to a playable URL. */
        public readonly string $downloadInfoUrl,
        public readonly bool $direct,
        public readonly ?Client $client = null,
    ) {
    }

    /**
     * Resolve the manifest into a URL the audio can be fetched from.
     *
     * The manifest expires roughly a minute after the download info was
     * fetched, after which this fails rather than returning a stale link.
     *
     * The signature scheme here is the one reverse-engineered years ago; the
     * service currently answers it with a redirect to its streaming hosts.
     * Should that stop being true, this is the method that breaks.
     *
     * @throws YandexMusicException when no client is attached or the manifest
     *                              cannot be read
     */
    public function directLink(): string
    {
        if (null === $this->client) {
            throw new YandexMusicException(
                'This DownloadInfo has no client attached, so it cannot resolve its own manifest.',
            );
        }

        $raw = $this->client->request->retrieve($this->downloadInfoUrl);

        // Parse errors are collected rather than emitted: a malformed manifest
        // is something to report, not something to warn about from inside a
        // library.
        $previous = libxml_use_internal_errors(true);

        try {
            $xml = simplexml_load_string($raw);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        if (false === $xml) {
            throw new YandexMusicException('The download manifest was not valid XML.');
        }

        $host = (string) $xml->host;
        $path = (string) $xml->path;
        $ts = (string) $xml->ts;
        $secret = (string) $xml->s;

        if ('' === $host || '' === $path || '' === $ts || '' === $secret) {
            throw new YandexMusicException(
                'The download manifest is missing fields. It has probably expired — these last about a minute.',
            );
        }

        $signature = md5(self::LINK_SALT.substr($path, 1).$secret);

        return sprintf('https://%s/get-mp3/%s/%s%s', $host, $signature, $ts, $path);
    }

    /**
     * Fetch the audio into a file, streaming rather than buffering it.
     *
     * @return int bytes written
     */
    public function download(string $path): int
    {
        if (null === $this->client) {
            throw new YandexMusicException('This DownloadInfo has no client attached, so it cannot download.');
        }

        return $this->client->request->download($this->directLink(), $path);
    }

    protected function identity(): array
    {
        return [$this->codec, $this->bitrateInKbps, $this->gain, $this->preview, $this->downloadInfoUrl];
    }
}
