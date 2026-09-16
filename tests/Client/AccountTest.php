<?php

declare(strict_types=1);

namespace LuckyWins\YandexMusic\Tests\Client;

use LuckyWins\YandexMusic\Client;
use LuckyWins\YandexMusic\Client\Account as AccountTrait;
use LuckyWins\YandexMusic\Http\Request;
use LuckyWins\YandexMusic\Model\Account\Status;
use LuckyWins\YandexMusic\Model\Account\UserSettings;
use LuckyWins\YandexMusic\Model\PermissionAlerts;
use LuckyWins\YandexMusic\Model\PromoCodeStatus;
use LuckyWins\YandexMusic\Model\Settings;
use LuckyWins\YandexMusic\Tests\Support\MockHttpClient;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AccountTrait::class)]
final class AccountTest extends TestCase
{
    private const STATUS = [
        'result' => [
            'account' => [
                'now' => '2026-09-16T12:00:00+00:00',
                'serviceAvailable' => true,
                'uid' => 1130000002804451,
                'login' => 'user@yandex.ru',
            ],
        ],
    ];

    public function testAccountStatusIsTyped(): void
    {
        $http = (new MockHttpClient())->queue(self::STATUS);

        $status = $this->client($http)->accountStatus();

        self::assertInstanceOf(Status::class, $status);
        self::assertSame('user@yandex.ru', $status->account?->login);
        self::assertSame(
            'https://api.music.yandex.net/account/status',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testInitFillsTheAccountId(): void
    {
        $http = (new MockHttpClient())->queue(self::STATUS);
        $client = $this->client($http);

        self::assertNull($client->getAccountUid(), 'nothing is loaded before init()');

        $returned = $client->init();

        self::assertSame($client, $returned, 'init() returns the client so it can be chained');
        self::assertSame(1130000002804451, $client->getAccountUid());
        self::assertInstanceOf(Status::class, $client->me());
    }

    /**
     * Constructing a client must not touch the network — that is why init()
     * exists as a separate step.
     */
    public function testConstructionMakesNoRequest(): void
    {
        $http = new MockHttpClient();

        new Client('y0_token', new Request($http));

        self::assertSame(0, $http->requestCount());
    }

    public function testAccountSettingsIsTyped(): void
    {
        $http = (new MockHttpClient())->queue(['result' => [
            'uid' => 1, 'lastFmScrobblingEnabled' => false, 'shuffleEnabled' => true, 'volumePercents' => 70,
            'modified' => 'm', 'facebookScrobblingEnabled' => false, 'addNewTrackOnPlaylistTop' => true,
            'userMusicVisibility' => 'public', 'userSocialVisibility' => 'public', 'rbtDisabled' => false,
            'theme' => 'black', 'promosDisabled' => true, 'autoPlayRadio' => true, 'syncQueueEnabled' => true,
        ]]);

        $settings = $this->client($http)->accountSettings();

        self::assertInstanceOf(UserSettings::class, $settings);
        self::assertSame('black', $settings->theme);
        self::assertSame('https://api.music.yandex.net/account/settings', (string) $http->lastRequest()->getUri());
    }

    /**
     * Booleans go over the wire capitalized, because that is what the reference
     * library sends and what is known to be accepted.
     */
    public function testSettingABooleanSendsItCapitalized(): void
    {
        $http = (new MockHttpClient())->queue(['result' => []]);

        $this->client($http)->accountSettingsSet('shuffleEnabled', true);

        self::assertSame('POST', $http->lastRequest()->getMethod());
        self::assertSame(['shuffleEnabled' => 'True'], $http->formBodyAt(0));
    }

    public function testSettingSeveralValuesAtOnce(): void
    {
        $http = (new MockHttpClient())->queue(['result' => []]);

        $this->client($http)->accountSettingsSetMany(['theme' => 'white', 'volumePercents' => 50, 'adsDisabled' => false]);

        self::assertSame(
            ['theme' => 'white', 'volumePercents' => '50', 'adsDisabled' => 'False'],
            $http->formBodyAt(0),
        );
    }

    /**
     * Two endpoints in this domain sit at the root rather than under /account.
     * Copying a neighbouring method is exactly how that gets broken.
     */
    public function testSettingsAndAlertsUseRootPaths(): void
    {
        $http = (new MockHttpClient())
            ->queue(['result' => ['webPaymentUrl' => 'https://pay', 'promoCodesEnabled' => true]])
            ->queue(['result' => ['alerts' => ['subscription-expired']]]);

        $client = $this->client($http);

        self::assertInstanceOf(Settings::class, $client->settings());
        self::assertSame('https://api.music.yandex.net/settings', (string) $http->requestAt(0)->getUri());

        self::assertInstanceOf(PermissionAlerts::class, $client->permissionAlerts());
        self::assertSame('https://api.music.yandex.net/permission-alerts', (string) $http->requestAt(1)->getUri());
    }

    public function testExperimentsComeBackAsABareMap(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['newLanding' => 'test', 'fastSearch' => 'control']]);

        self::assertSame(
            ['newLanding' => 'test', 'fastSearch' => 'control'],
            $this->client($http)->accountExperiments(),
        );
    }

    public function testPromoCodeUsesTheClientLanguageByDefault(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['status' => 'ok', 'statusDesc' => 'Готово']]);

        $status = $this->client($http, 'en')->consumePromoCode('SOMECODE');

        self::assertInstanceOf(PromoCodeStatus::class, $status);
        self::assertSame(['code' => 'SOMECODE', 'language' => 'en'], $http->formBodyAt(0));
    }

    public function testPromoCodeLanguageCanBeOverridden(): void
    {
        $http = (new MockHttpClient())->queue(['result' => ['status' => 'ok', 'statusDesc' => 'Готово']]);

        $this->client($http, 'en')->consumePromoCode('SOMECODE', 'ru');

        self::assertSame(['code' => 'SOMECODE', 'language' => 'ru'], $http->formBodyAt(0));
    }

    /**
     * The radio endpoint answers with the same model, so it is typed too even
     * though it still lives in the legacy trait.
     */
    public function testRotorAccountStatusIsTypedAsWell(): void
    {
        $http = (new MockHttpClient())->queue(self::STATUS);

        self::assertInstanceOf(Status::class, $this->client($http)->rotorAccountStatus());
        self::assertSame(
            'https://api.music.yandex.net/rotor/account/status',
            (string) $http->lastRequest()->getUri(),
        );
    }

    private function client(MockHttpClient $http, string $language = 'ru'): Client
    {
        return new Client('y0_token', new Request($http), $language);
    }
}
