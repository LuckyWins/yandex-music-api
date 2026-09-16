# Models

Generated from the source by `make docs` — do not edit. Decisions and
divergences from the Python reference are written by hand under
[porting/](porting/).

A required field missing from a response raises rather than defaulting: it
means the API changed shape. List fields default to empty, object fields to
null.

## Account

### Account

The person behind the token.

| Field | Type | Required | Notes |
|---|---|---|---|
| `now` | `string` | **yes** |  |
| `serviceAvailable` | `bool` | **yes** |  |
| `region` | `?int` | no |  |
| `uid` | `?int` | no |  |
| `login` | `?string` | no |  |
| `fullName` | `?string` | no |  |
| `secondName` | `?string` | no |  |
| `firstName` | `?string` | no |  |
| `displayName` | `?string` | no |  |
| `hostedUser` | `?bool` | no |  |
| `birthday` | `?string` | no |  |
| `passportPhones` | `list<PassportPhone>` | no | list of [PassportPhone](#passportphone) |
| `registeredAt` | `?string` | no |  |
| `hasInfoForAppMetrica` | `?bool` | no |  |
| `child` | `?bool` | no |  |
| `regionCode` | `?string` | no | Two-letter country code, alongside the numeric `region`. |
| `nonOwnerFamilyMember` | `?bool` | no | True on a seat in someone else's family subscription. |

### Alert

A banner the app is asked to show, such as a subscription prompt.

| Field | Type | Required | Notes |
|---|---|---|---|
| `alertId` | `string` | **yes** |  |
| `text` | `string` | **yes** |  |
| `bgColor` | `string` | **yes** |  |
| `textColor` | `string` | **yes** |  |
| `alertType` | `string` | **yes** |  |
| `button` | `?AlertButton` | no | [AlertButton](#alertbutton) |
| `closeButton` | `?bool` | no |  |

### AlertButton

The button on an alert banner.

| Field | Type | Required | Notes |
|---|---|---|---|
| `text` | `string` | **yes** |  |
| `bgColor` | `string` | **yes** |  |
| `textColor` | `string` | **yes** |  |
| `uri` | `string` | **yes** |  |

### AutoRenewable

A subscription that renews itself until cancelled.

| Field | Type | Required | Notes |
|---|---|---|---|
| `expires` | `string` | **yes** |  |
| `vendor` | `string` | **yes** |  |
| `vendorHelpUrl` | `string` | **yes** |  |
| `finished` | `bool` | **yes** |  |
| `product` | `?Product` | no | [Product](#product) |
| `masterInfo` | `?User` | no | [User](#user). Whose family subscription this seat belongs to. |
| `productId` | `?string` | no |  |
| `orderId` | `?int` | no |  |

### Deactivation

How to cancel a subscription bought through a mobile operator.

| Field | Type | Required | Notes |
|---|---|---|---|
| `method` | `string` | **yes** |  |
| `instructions` | `?string` | no |  |

### NonAutoRenewable

The window of a subscription that will not renew itself.

| Field | Type | Required | Notes |
|---|---|---|---|
| `start` | `string` | **yes** |  |
| `end` | `string` | **yes** |  |

### Operator

A subscription billed through a mobile operator.

| Field | Type | Required | Notes |
|---|---|---|---|
| `productId` | `string` | **yes** |  |
| `phone` | `string` | **yes** |  |
| `paymentRegularity` | `string` | **yes** |  |
| `title` | `string` | **yes** |  |
| `suspended` | `bool` | **yes** |  |
| `deactivation` | `list<Deactivation>` | no | list of [Deactivation](#deactivation). How to cancel. |

### PassportPhone

A phone number attached to the Yandex ID behind the account.

| Field | Type | Required | Notes |
|---|---|---|---|
| `phone` | `string` | **yes** |  |

### Permissions

What the account is allowed to do, and until when.

| Field | Type | Required | Notes |
|---|---|---|---|
| `until` | `string` | **yes** |  |
| `values` | `list<string>` | **yes** |  |
| `default` | `list<string>` | **yes** |  |

### Plus

Whether the account has Yandex Plus, the subscription the music service sits under.

| Field | Type | Required | Notes |
|---|---|---|---|
| `hasPlus` | `bool` | **yes** |  |
| `isTutorialCompleted` | `bool` | **yes** |  |

### Price

An amount of money, in the smallest unit of its currency.

| Field | Type | Required | Notes |
|---|---|---|---|
| `amount` | `int` | **yes** |  |
| `currency` | `string` | **yes** |  |

### Product

A subscription plan on offer.

| Field | Type | Required | Notes |
|---|---|---|---|
| `productId` | `string` | **yes** |  |
| `type` | `string` | **yes** |  |
| `duration` | `int` | **yes** |  |
| `trialDuration` | `int` | **yes** |  |
| `feature` | `string` | **yes** |  |
| `debug` | `bool` | **yes** |  |
| `plus` | `bool` | **yes** |  |
| `price` | `?Price` | no | [Price](#price) |
| `commonPeriodDuration` | `?string` | no |  |
| `cheapest` | `?bool` | no |  |
| `title` | `?string` | no |  |
| `familySub` | `?bool` | no |  |
| `fbImage` | `?string` | no |  |
| `fbName` | `?string` | no |  |
| `family` | `?bool` | no |  |
| `features` | `list<string>|null` | no | Null when absent, unlike paymentMethodTypes below, which is empty. |
| `description` | `?string` | no |  |
| `available` | `?bool` | no |  |
| `trialAvailable` | `?bool` | no |  |
| `trialPeriodDuration` | `?string` | no |  |
| `introPeriodDuration` | `?string` | no |  |
| `introPrice` | `?Price` | no | [Price](#price) |
| `startPeriodDuration` | `?string` | no |  |
| `startPrice` | `?Price` | no | [Price](#price) |
| `vendorTrialAvailable` | `?bool` | no |  |
| `buttonText` | `?string` | no |  |
| `buttonAdditionalText` | `?string` | no |  |
| `licenceTextParts` | `list<LicenceTextPart>` | no | list of [LicenceTextPart](#licencetextpart) |
| `paymentMethodTypes` | `list<string>` | no |  |
| `offersPositionId` | `?string` | no | Which slot this offer occupied in the batch it was served in. |

### RenewableRemainder

How much of a subscription is left.

| Field | Type | Required | Notes |
|---|---|---|---|
| `days` | `int` | **yes** |  |

### Status

The whole picture of an account: who it is, what it may do, what it pays for.

| Field | Type | Required | Notes |
|---|---|---|---|
| `account` | `?Account` | no | [Account](#account) |
| `permissions` | `?Permissions` | no | [Permissions](#permissions) |
| `advertisement` | `?string` | no |  |
| `subscription` | `?Subscription` | no | [Subscription](#subscription) |
| `cacheLimit` | `?int` | no |  |
| `subeditor` | `?bool` | no |  |
| `subeditorLevel` | `?int` | no |  |
| `plus` | `?Plus` | no | [Plus](#plus) |
| `defaultEmail` | `?string` | no |  |
| `skipsPerHour` | `?int` | no |  |
| `stationExists` | `?bool` | no |  |
| `stationData` | `?StationData` | no | [StationData](#stationdata) |
| `barBelow` | `?Alert` | no | [Alert](#alert). The banner to show below the player, if Yandex wants one shown. |
| `premiumRegion` | `?int` | no |  |
| `experiment` | `?int` | no |  |
| `pretrialActive` | `?bool` | no |  |
| `userhash` | `?string` | no |  |
| `masterhub` | `array<string,` | no | Subscriptions across the wider Yandex ecosystem, not just music. |
| `hasOptions` | `list<mixed>|null` | no | Undocumented, absent from the reference library, and empty on the accounts seen so far. |

### Subscription

Everything about what the account is currently paying for.

| Field | Type | Required | Notes |
|---|---|---|---|
| `hadAnySubscription` | `bool` | **yes** |  |
| `nonAutoRenewableRemainder` | `?RenewableRemainder` | no | [RenewableRemainder](#renewableremainder) |
| `autoRenewable` | `list<AutoRenewable>` | no | list of [AutoRenewable](#autorenewable) |
| `familyAutoRenewable` | `list<AutoRenewable>` | no | list of [AutoRenewable](#autorenewable) |
| `operator` | `list<Operator>` | no | list of [Operator](#operator). Operator-billed subscriptions. |
| `nonAutoRenewable` | `?NonAutoRenewable` | no | [NonAutoRenewable](#nonautorenewable) |
| `canStartTrial` | `?bool` | no |  |
| `mcdonalds` | `?bool` | no |  |
| `end` | `?string` | no |  |

### UserSettings

The account's own preferences, as set in the apps.

| Field | Type | Required | Notes |
|---|---|---|---|
| `uid` | `int` | **yes** |  |
| `lastFmScrobblingEnabled` | `bool` | **yes** |  |
| `shuffleEnabled` | `bool` | **yes** |  |
| `volumePercents` | `int` | **yes** |  |
| `modified` | `string` | **yes** |  |
| `facebookScrobblingEnabled` | `bool` | **yes** |  |
| `addNewTrackOnPlaylistTop` | `bool` | **yes** |  |
| `userMusicVisibility` | `string` | **yes** | `private` or `public`. |
| `userSocialVisibility` | `string` | **yes** | `private` or `public`. |
| `rbtDisabled` | `bool` | **yes** |  |
| `theme` | `string` | **yes** | `white` or `black`. |
| `promosDisabled` | `bool` | **yes** |  |
| `autoPlayRadio` | `bool` | **yes** |  |
| `syncQueueEnabled` | `bool` | **yes** |  |
| `adsDisabled` | `?bool` | no |  |
| `diskEnabled` | `?bool` | no |  |
| `showDiskTracksInLibrary` | `?bool` | no |  |
| `explicitForbidden` | `?bool` | no | Whether explicit content is blocked. |
| `childModEnabled` | `?bool` | no |  |
| `childModeChangedByUser` | `?bool` | no |  |
| `wizardIsPassed` | `?bool` | no | Whether the taste-picking wizard has been completed. |
| `userCollectionHue` | `?int` | no | The colour the collection is tinted with, as a hue. |
| `aiContentReductionEnabled` | `?bool` | no |  |

## DeviceAuth

### DeviceCode

The first step of the OAuth device flow.

| Field | Type | Required | Notes |
|---|---|---|---|
| `deviceCode` | `string` | **yes** | Opaque code identifying this authorization attempt, sent back when polling. |
| `userCode` | `string` | **yes** | Short code the user types into the verification page. |
| `verificationUrl` | `string` | **yes** | Page the user opens to confirm. |
| `expiresIn` | `int` | **yes** | Seconds until this code stops being accepted. |
| `interval` | `int` | **yes** | Seconds to wait between polls. |

### OAuthToken

The token returned once the user has confirmed the device code.

| Field | Type | Required | Notes |
|---|---|---|---|
| `accessToken` | `string` | **yes** |  |
| `refreshToken` | `?string` | no |  |
| `expiresIn` | `?int` | no | Seconds until the access token expires — typically about a year. |
| `tokenType` | `?string` | no | Usually `bearer`, though Yandex expects the `OAuth` scheme in requests. |

## Experiment

### ExperimentDetail

One A/B experiment the account has been placed into.

| Field | Type | Required | Notes |
|---|---|---|---|
| `group` | `?string` | no |  |
| `value` | `?ExperimentDetailValue` | no | [ExperimentDetailValue](#experimentdetailvalue) |

### ExperimentDetailValue

The configuration an experiment was given.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `?string` | no |  |
| `parameters` | `array<string,` | no | Everything the experiment was configured with, exactly as sent. |

### ExperimentsDetails

The A/B experiments an account is in, keyed by experiment name.

| Field | Type | Required | Notes |
|---|---|---|---|
| `experiments` | `array<string,` | no |  |

## Playlist

### User

A Yandex.Music user.

| Field | Type | Required | Notes |
|---|---|---|---|
| `uid` | `int` | **yes** |  |
| `login` | `string` | **yes** |  |
| `name` | `?string` | no |  |
| `displayName` | `?string` | no |  |
| `fullName` | `?string` | no |  |
| `sex` | `?string` | no |  |
| `verified` | `?bool` | no |  |
| `regions` | `list<int>` | no |  |

## Rotor

### StationData

The personal radio station attached to an account.

| Field | Type | Required | Notes |
|---|---|---|---|
| `name` | `string` | **yes** |  |

## Top level

### PermissionAlerts

Warnings the service wants shown to the account.

| Field | Type | Required | Notes |
|---|---|---|---|
| `alerts` | `list<string>` | no |  |

### PromoCodeStatus

What came of redeeming a promo code.

| Field | Type | Required | Notes |
|---|---|---|---|
| `status` | `string` | **yes** |  |
| `statusDesc` | `string` | **yes** |  |
| `accountStatus` | `?Status` | no | [Status](#status) |

### Settings

What the account can be sold, and where to buy it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `webPaymentUrl` | `string` | **yes** |  |
| `promoCodesEnabled` | `bool` | **yes** |  |
| `inAppProducts` | `list<Product>` | no | list of [Product](#product) |
| `nativeProducts` | `list<Product>` | no | list of [Product](#product) |
| `webPaymentMonthProductPrice` | `?Price` | no | [Price](#price) |
| `offersBatchId` | `?string` | no | Identifies the batch of offers this response was generated from. |

## Track

### LicenceTextPart

One run of text in a licence notice, optionally a link.

| Field | Type | Required | Notes |
|---|---|---|---|
| `text` | `string` | **yes** |  |
| `url` | `?string` | no |  |

