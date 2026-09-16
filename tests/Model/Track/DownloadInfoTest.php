<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Model\Track;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Exception\YandexMusicException;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\Track\DownloadInfo;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
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
     * The URL is built from the manifest by a scheme reverse-engineered years
     * ago and still accepted by the service — verified by downloading a real
     * track. Pinning it here means a change to the algorithm shows up as a
     * failing test rather than as audio that will not play.
     */
    public function testDirectLinkIsBuiltFromTheManifest(): void
    {
        $http = (new MockHttpClient())->queue(
            '<?xml version="1.0" encoding="utf-8"?><download-info>'
            .'<host>strm.example.invalid</host>'
            .'<path>/test-path/file.mp3</path>'
            .'<ts>1a0ab00671a</ts>'
            .'<s>abc123</s>'
            .'</download-info>',
        );

        $info = self::infoWith($http);

        self::assertSame(
            'https://strm.example.invalid/get-mp3/aea1b1cd3be9a7124f65c409f1550558/1a0ab00671a/test-path/file.mp3',
            $info->directLink(),
        );
    }

    /**
     * A manifest lasts about a minute. Past that the service answers with
     * something that parses but has nothing in it, and the failure should say
     * what actually happened.
     */
    public function testAnExpiredManifestIsReportedAsSuch(): void
    {
        $http = (new MockHttpClient())->queue('<?xml version="1.0"?><download-info></download-info>');

        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('expired');

        self::infoWith($http)->directLink();
    }

    public function testAManifestThatIsNotXmlIsReported(): void
    {
        $http = (new MockHttpClient())->queue('not xml at all');

        $this->expectException(YandexMusicException::class);
        $this->expectExceptionMessage('not valid XML');

        self::infoWith($http)->directLink();
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

    private static function infoWith(MockHttpClient $http): DownloadInfo
    {
        return new DownloadInfo(
            'mp3',
            320,
            false,
            false,
            'https://storage.invalid/file-download-info/x',
            false,
            new Client(request: new Request($http)),
        );
    }
}
