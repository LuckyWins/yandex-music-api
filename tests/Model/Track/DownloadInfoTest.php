<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\DownloadInfo;
use LuckyWins\YandexMusic\Tests\Support\ModelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DownloadInfo::class)]
final class DownloadInfoTest extends ModelTestCase
{
    protected static function modelClass(): string
    {
        return DownloadInfo::class;
    }

    protected static function fullPayload(): array
    {
        return ['codec' => 'mp3', 'bitrateInKbps' => 320, 'gain' => false, 'preview' => false, 'downloadInfoUrl' => 'https://storage.mds.yandex.net/file-download-info/x', 'direct' => false];
    }

    protected static function requiredPayload(): array
    {
        return ['codec' => 'mp3', 'bitrateInKbps' => 320, 'gain' => false, 'preview' => false, 'downloadInfoUrl' => 'https://storage.mds.yandex.net/file-download-info/x', 'direct' => false];
    }

    protected function assertFullyPopulated(Model $model): void
    {
        self::assertInstanceOf(DownloadInfo::class, $model);
        self::assertSame('mp3', $model->codec);
        self::assertSame(320, $model->bitrateInKbps);
        self::assertFalse($model->preview);
    }

    protected function equalityTriple(): array
    {
        return [new DownloadInfo('mp3', 320, false, false, 'u', false), new DownloadInfo('mp3', 320, false, false, 'u', false), new DownloadInfo('mp3', 192, false, false, 'u', false)];
    }

    /**
     * Resolving a manifest needs the client that fetched it; without one the
     * failure should say so rather than dereferencing null.
     */
    public function testResolvingWithoutAClientIsRefusedClearly(): void
    {
        $model = new DownloadInfo('mp3', 320, false, false, 'https://example.invalid/x', false);

        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('no client attached');

        $model->directLink();
    }
}
