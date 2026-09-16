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

## Album

### Album

An album.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?int` | no |  |
| `error` | `?string` | no |  |
| `title` | `?string` | no |  |
| `trackCount` | `?int` | no |  |
| `artists` | `list<Artist>` | no | list of [Artist](#artist) |
| `labels` | `list<Label>|list<string>` | no | Either full label objects or bare names, depending on the endpoint. |
| `available` | `?bool` | no |  |
| `availableForPremiumUsers` | `?bool` | no |  |
| `version` | `?string` | no |  |
| `coverUri` | `?string` | no |  |
| `contentWarning` | `?string` | no |  |
| `originalReleaseYear` | `mixed` | no |  |
| `genre` | `?string` | no |  |
| `textColor` | `?string` | no |  |
| `shortDescription` | `?string` | no |  |
| `description` | `?string` | no |  |
| `isPremiere` | `?bool` | no |  |
| `isBanner` | `?bool` | no |  |
| `metaType` | `?string` | no |  |
| `storageDir` | `?string` | no |  |
| `ogImage` | `?string` | no |  |
| `buy` | `list<mixed>|null` | no |  |
| `recent` | `?bool` | no |  |
| `veryImportant` | `?bool` | no |  |
| `availableForMobile` | `?bool` | no |  |
| `availablePartially` | `?bool` | no |  |
| `bests` | `list<int>|null` | no |  |
| `duplicates` | `list<Album>` | no | list of [Album](#album) |
| `prerolls` | `list<mixed>|null` | no |  |
| `volumes` | `list<list<Track>>|null` | no | Tracks grouped by disc: one inner list per volume. |
| `year` | `?int` | no |  |
| `releaseDate` | `?string` | no |  |
| `type` | `?string` | no |  |
| `trackPosition` | `?TrackPosition` | no | [TrackPosition](#trackposition) |
| `regions` | `list<string>|null` | no |  |
| `availableAsRbt` | `?bool` | no |  |
| `lyricsAvailable` | `?bool` | no |  |
| `rememberPosition` | `?bool` | no |  |
| `albums` | `list<Album>` | no | list of [Album](#album) |
| `durationMs` | `?int` | no |  |
| `explicit` | `?bool` | no |  |
| `startDate` | `?string` | no |  |
| `likesCount` | `?int` | no |  |
| `deprecation` | `?Deprecation` | no | [Deprecation](#deprecation) |
| `availableRegions` | `list<string>|null` | no |  |
| `availableForOptions` | `list<string>|null` | no |  |
| `listeningFinished` | `?bool` | no |  |
| `disclaimers` | `list<string>|null` | no |  |
| `actionButton` | `?AlbumActionButton` | no | [AlbumActionButton](#albumactionbutton) |

### AlbumActionButton

A call to action shown on an album, such as a pre-save prompt.

| Field | Type | Required | Notes |
|---|---|---|---|
| `text` | `?string` | no |  |
| `url` | `?string` | no |  |
| `color` | `?string` | no |  |

### Deprecation

Where an album has been superseded by another one.

| Field | Type | Required | Notes |
|---|---|---|---|
| `targetAlbumId` | `?int` | no |  |
| `status` | `?string` | no |  |
| `done` | `?bool` | no |  |

### TrackPosition

Where a track sits on an album: which disc, and which slot on it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `volume` | `int` | **yes** |  |
| `index` | `int` | **yes** |  |

## Artist

### Artist

An artist.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?int` | no |  |
| `error` | `?string` | no |  |
| `reason` | `?string` | no |  |
| `name` | `?string` | no |  |
| `cover` | `?Cover` | no | [Cover](#cover) |
| `various` | `?bool` | no | Whether this stands for an assortment of artists rather than one. |
| `composer` | `?bool` | no |  |
| `genres` | `list<string>|null` | no |  |
| `ogImage` | `?string` | no |  |
| `opImage` | `?string` | no |  |
| `noPicturesFromSearch` | `mixed` | no | Set only when the artist comes back from a search. |
| `counts` | `?Counts` | no | [Counts](#counts) |
| `available` | `?bool` | no |  |
| `ratings` | `?Ratings` | no | [Ratings](#ratings) |
| `links` | `list<Link>|null` | no | list of [Link](#link) |
| `ticketsAvailable` | `?bool` | no |  |
| `likesCount` | `?int` | no |  |
| `popularTracks` | `list<Track>|null` | no | list of [Track](#track) |
| `regions` | `list<string>|null` | no |  |
| `decomposed` | `list<Artist|string>|null` | no | A credit line broken into pieces: artists interleaved with the words that join them, such as `feat.`. |
| `fullNames` | `mixed` | no |  |
| `handMadeDescription` | `?string` | no |  |
| `description` | `?Description` | no | [Description](#description) |
| `countries` | `list<string>|null` | no |  |
| `enWikipediaLink` | `?string` | no |  |
| `dbAliases` | `list<string>|null` | no |  |
| `aliases` | `mixed` | no |  |
| `initDate` | `?string` | no |  |
| `endDate` | `?string` | no |  |
| `yaMoneyId` | `?string` | no |  |
| `disclaimers` | `list<string>|null` | no |  |
| `contentRestrictions` | `?ContentRestrictions` | no | [ContentRestrictions](#contentrestrictions) |
| `cutoutCover` | `?Cover` | no | [Cover](#cover) |

### Counts

How much of an artist there is to listen to.

| Field | Type | Required | Notes |
|---|---|---|---|
| `tracks` | `int` | **yes** |  |
| `directAlbums` | `int` | **yes** |  |
| `alsoAlbums` | `int` | **yes** |  |
| `alsoTracks` | `int` | **yes** |  |

### Description

An artist's biography, usually lifted from Wikipedia.

| Field | Type | Required | Notes |
|---|---|---|---|
| `text` | `string` | **yes** |  |
| `uri` | `?string` | no |  |

### Link

A link from an artist's page — their site, or a social account.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `string` | **yes** |  |
| `href` | `string` | **yes** |  |
| `type` | `string` | **yes** |  |
| `socialNetwork` | `?string` | no |  |

### Ratings

An artist's position in the charts.

| Field | Type | Required | Notes |
|---|---|---|---|
| `month` | `int` | **yes** |  |
| `week` | `?int` | no |  |
| `day` | `?int` | no |  |

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

## Label

### Label

A record label.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `int` | **yes** |  |
| `name` | `string` | **yes** |  |
| `description` | `?string` | no |  |
| `descriptionFormatted` | `?string` | no |  |
| `image` | `?string` | no |  |
| `links` | `list<Link>|null` | no | list of [Link](#link) |
| `type` | `?string` | no |  |

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

## Shot

### Shot

An interjection scheduled to play after a track.

| Field | Type | Required | Notes |
|---|---|---|---|
| `order` | `int` | **yes** |  |
| `played` | `bool` | **yes** |  |
| `shotId` | `string` | **yes** |  |
| `status` | `string` | **yes** |  |
| `shotData` | `?ShotData` | no | [ShotData](#shotdata) |

### ShotData

The content of one of Alice's spoken interjections between tracks.

| Field | Type | Required | Notes |
|---|---|---|---|
| `coverUri` | `string` | **yes** |  |
| `mdsUrl` | `string` | **yes** | Where the audio lives. |
| `shotText` | `string` | **yes** |  |
| `shotType` | `?ShotType` | no | [ShotType](#shottype) |

### ShotEvent

What the service wants played between two tracks.

| Field | Type | Required | Notes |
|---|---|---|---|
| `eventId` | `string` | **yes** |  |
| `shots` | `list<Shot>` | no | list of [Shot](#shot) |

### ShotType

What kind of interjection a shot is.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `string` | **yes** |  |
| `title` | `string` | **yes** |  |

## Supplement

### Lyrics

Lyrics as returned alongside a track's supplement.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `int` | **yes** |  |
| `lyrics` | `string` | **yes** | The opening lines only. |
| `fullLyrics` | `string` | **yes** |  |
| `hasRights` | `bool` | **yes** |  |
| `showTranslation` | `bool` | **yes** |  |
| `textLanguage` | `?string` | no |  |
| `url` | `?string` | no | Where a translation came from, usually genius.com. |

### Supplement

Extra material attached to a track: videos, a podcast description, and — deprecated — its lyrics.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `int` | **yes** |  |
| `lyrics` | `?Lyrics` | no | [Lyrics](#lyrics) |
| `videos` | `list<VideoSupplement>` | no | list of [VideoSupplement](#videosupplement) |
| `radioIsAvailable` | `?bool` | no |  |
| `description` | `?string` | no | The full text for a podcast episode. |

### VideoSupplement

A video tied to a track, usually its official clip.

| Field | Type | Required | Notes |
|---|---|---|---|
| `cover` | `string` | **yes** |  |
| `provider` | `string` | **yes** |  |
| `title` | `?string` | no |  |
| `providerVideoId` | `?string` | no |  |
| `url` | `?string` | no |  |
| `embedUrl` | `?string` | no | Hosted by Yandex rather than the provider. |
| `embed` | `?string` | no | Ready-made HTML for embedding. |

## Top level

### ContentRestrictions

Why something may not be playable here.

| Field | Type | Required | Notes |
|---|---|---|---|
| `available` | `?bool` | no |  |
| `disclaimers` | `list<string>|null` | no |  |

### Cover

Artwork, as a template rather than a finished URL.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |
| `uri` | `?string` | no |  |
| `itemsUri` | `list<string>|null` | no |  |
| `dir` | `?string` | no |  |
| `version` | `?string` | no |  |
| `custom` | `?bool` | no |  |
| `isCustom` | `?bool` | no |  |
| `copyrightName` | `?string` | no |  |
| `copyrightCline` | `?string` | no |  |
| `prefix` | `?string` | no |  |
| `error` | `?string` | no |  |
| `color` | `?string` | no |  |
| `derivedColors` | `?CoverDerivedColors` | no | [CoverDerivedColors](#coverderivedcolors) |
| `videoUrl` | `?string` | no |  |

### CoverDerivedColors

Colours pulled out of cover art, so an interface can tint itself to match.

| Field | Type | Required | Notes |
|---|---|---|---|
| `average` | `?string` | no |  |
| `waveText` | `?string` | no |  |
| `miniPlayer` | `?string` | no |  |
| `accent` | `?string` | no |  |

### Credit

One line of a credit list: who did what.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `?string` | no |  |
| `value` | `?string` | no |  |

### Credits

Everyone credited on a recording.

| Field | Type | Required | Notes |
|---|---|---|---|
| `credits` | `list<Credit>` | no | list of [Credit](#credit) |

### Disclaimer

Notices that must accompany a recording.

| Field | Type | Required | Notes |
|---|---|---|---|
| `foreignAgent` | `?ForeignAgent` | no | [ForeignAgent](#foreignagent) |

### ForeignAgent

The notice Russian law requires be shown for material by someone designated a foreign agent.

| Field | Type | Required | Notes |
|---|---|---|---|
| `reason` | `?string` | no |  |
| `title` | `?string` | no |  |

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

### DownloadInfo

One way a track can be fetched: a codec, a bitrate, and a manifest to resolve into an actual URL.

| Field | Type | Required | Notes |
|---|---|---|---|
| `codec` | `string` | **yes** | `mp3` or `aac`. |
| `bitrateInKbps` | `int` | **yes** | 64, 128, 192 or 320. |
| `gain` | `bool` | **yes** |  |
| `preview` | `bool` | **yes** |  |
| `downloadInfoUrl` | `string` | **yes** | The XML manifest that resolves to a playable URL. |
| `direct` | `bool` | **yes** |  |

### Fade

Where a track fades in and out, in seconds from its start.

| Field | Type | Required | Notes |
|---|---|---|---|
| `inStart` | `?float` | no |  |
| `inStop` | `?float` | no |  |
| `outStart` | `?float` | no |  |
| `outStop` | `?float` | no |  |

### LicenceTextPart

One run of text in a licence notice, optionally a link.

| Field | Type | Required | Notes |
|---|---|---|---|
| `text` | `string` | **yes** |  |
| `url` | `?string` | no |  |

### LyricsInfo

Which kinds of lyrics exist for a track.

| Field | Type | Required | Notes |
|---|---|---|---|
| `hasAvailableSyncLyrics` | `bool` | **yes** |  |
| `hasAvailableTextLyrics` | `bool` | **yes** |  |

### LyricsMajor

Who supplied a set of lyrics.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `int` | **yes** |  |
| `name` | `string` | **yes** |  |
| `prettyName` | `string` | **yes** |  |

### Major

The label that released a track.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `int` | **yes** |  |
| `name` | `string` | **yes** |  |

### MetaData

Tags carried by a track a user uploaded themselves.

| Field | Type | Required | Notes |
|---|---|---|---|
| `album` | `?string` | no |  |
| `volume` | `?int` | no |  |
| `year` | `?int` | no |  |
| `number` | `?int` | no |  |
| `genre` | `?string` | no |  |
| `lyricist` | `?string` | no |  |
| `version` | `?string` | no |  |
| `composer` | `?string` | no |  |

### Normalization

Replay-gain figures for a track.

| Field | Type | Required | Notes |
|---|---|---|---|
| `gain` | `float` | **yes** |  |
| `peak` | `int` | **yes** |  |

### PoetryLoverMatch

Where a searched-for phrase sits inside the lyrics.

| Field | Type | Required | Notes |
|---|---|---|---|
| `begin` | `int` | **yes** |  |
| `end` | `int` | **yes** |  |
| `line` | `int` | **yes** |  |

### R128

Loudness measured to the EBU R 128 standard, for playing tracks at an even volume: `i` is integrated loudness, `tp` the true peak.

| Field | Type | Required | Notes |
|---|---|---|---|
| `i` | `float` | **yes** |  |
| `tp` | `float` | **yes** |  |

### SimilarTracks

What else sounds like a given track.

| Field | Type | Required | Notes |
|---|---|---|---|
| `track` | `?Track` | no | [Track](#track) |
| `similarTracks` | `list<Track>` | no | list of [Track](#track) |

### SmartPreviewParams

How to cut a short preview out of a track.

| Field | Type | Required | Notes |
|---|---|---|---|
| `durationMs` | `?int` | no |  |
| `fade` | `?Fade` | no | [Fade](#fade) |

### Track

A track.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `mixed` | **yes** |  |
| `title` | `?string` | no |  |
| `available` | `?bool` | no |  |
| `artists` | `list<Artist>` | no | list of [Artist](#artist) |
| `albums` | `list<Album>` | no | list of [Album](#album) |
| `availableForPremiumUsers` | `?bool` | no |  |
| `lyricsAvailable` | `?bool` | no |  |
| `poetryLoverMatches` | `list<PoetryLoverMatch>` | no | list of [PoetryLoverMatch](#poetrylovermatch) |
| `best` | `?bool` | no |  |
| `realId` | `mixed` | no |  |
| `ogImage` | `?string` | no |  |
| `type` | `?string` | no | Known value: `music`. |
| `coverUri` | `?string` | no |  |
| `major` | `?Major` | no | [Major](#major) |
| `durationMs` | `?int` | no |  |
| `storageDir` | `?string` | no |  |
| `fileSize` | `?int` | no |  |
| `substituted` | `?self` | no | [Track](#track). What plays instead, where the original is unavailable here. |
| `matchedTrack` | `?self` | no | [Track](#track) |
| `normalization` | `?Normalization` | no | [Normalization](#normalization) |
| `error` | `?string` | no |  |
| `canPublish` | `?bool` | no |  |
| `state` | `?string` | no |  |
| `desiredVisibility` | `?string` | no |  |
| `filename` | `?string` | no |  |
| `userInfo` | `?User` | no | [User](#user) |
| `metaData` | `?MetaData` | no | [MetaData](#metadata) |
| `regions` | `list<string>|null` | no |  |
| `availableAsRbt` | `?bool` | no |  |
| `contentWarning` | `?string` | no | Known value: `explicit`. |
| `explicit` | `?bool` | no |  |
| `previewDurationMs` | `?int` | no |  |
| `availableFullWithoutPermission` | `?bool` | no |  |
| `version` | `?string` | no |  |
| `rememberPosition` | `?bool` | no |  |
| `backgroundVideoUri` | `?string` | no |  |
| `shortDescription` | `?string` | no |  |
| `isSuitableForChildren` | `?bool` | no |  |
| `trackSource` | `?string` | no | Known values: `OWN`, `OWN_REPLACED_TO_UGC`. |
| `availableForOptions` | `list<string>|null` | no |  |
| `r128` | `?R128` | no | [R128](#r128) |
| `lyricsInfo` | `?LyricsInfo` | no | [LyricsInfo](#lyricsinfo) |
| `trackSharingFlag` | `?string` | no | Known values: `VIDEO_ALLOWED`, `COVER_ONLY`. |
| `derivedColors` | `?CoverDerivedColors` | no | [CoverDerivedColors](#coverderivedcolors) |
| `fade` | `?Fade` | no | [Fade](#fade) |
| `smartPreviewParams` | `?SmartPreviewParams` | no | [SmartPreviewParams](#smartpreviewparams) |
| `specialAudioResources` | `list<string>|null` | no |  |
| `disclaimers` | `list<string>|null` | no |  |
| `backgroundVideoId` | `?string` | no |  |
| `playerId` | `?string` | no |  |

### TrackFullInfo

A track with everything the service knows about it gathered in one place.

| Field | Type | Required | Notes |
|---|---|---|---|
| `track` | `?Track` | no | [Track](#track) |
| `similarTracks` | `list<Track>` | no | list of [Track](#track) |
| `alsoInAlbums` | `list<Track>` | no | list of [Track](#track) |
| `aliases` | `list<string>` | no |  |
| `artists` | `list<Artist>` | no | list of [Artist](#artist) |

### TrackLyrics

A pointer to a track's lyrics — the text itself is not in the response.

| Field | Type | Required | Notes |
|---|---|---|---|
| `downloadUrl` | `string` | **yes** |  |
| `lyricId` | `int` | **yes** |  |
| `externalLyricId` | `string` | **yes** |  |
| `writers` | `list<string>` | **yes** |  |
| `major` | `?LyricsMajor` | no | [LyricsMajor](#lyricsmajor) |

### TrackTrailer

A trailer introducing a track or episode.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `?string` | no |  |
| `track` | `?Track` | no | [Track](#track) |

