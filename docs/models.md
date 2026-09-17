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
| `migrated` | `?bool` | no | Only ever seen null, so its type is a reading of the name. |

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
| `hadAnySubscription` | `?bool` | no | Optional despite the reference declaring it required: the radio's own view of the account omits it, and a subscription that cannot be deserialized takes the whole status with it. |
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
| `actionButton` | `?ActionButton` | no | [ActionButton](#actionbutton) |
| `cover` | `?Cover` | no | [Cover](#cover). The cover as an object; `coverUri` carries the same art as a template. |
| `derivedColors` | `?CoverDerivedColors` | no | [CoverDerivedColors](#coverderivedcolors) |
| `trailer` | `?Trailer` | no | [Trailer](#trailer) |
| `hasTrailer` | `?bool` | no |  |
| `childContent` | `?bool` | no |  |
| `contentRestrictions` | `?ContentRestrictions` | no | [ContentRestrictions](#contentrestrictions) |
| `customWave` | `?CustomWave` | no | [CustomWave](#customwave) |
| `pager` | `?Pager` | no | [Pager](#pager). Present when the album arrives as one page of a longer list. |
| `metaTagId` | `?string` | no |  |
| `sortOrder` | `?string` | no |  |

### AlbumSimilarEntities

What to listen to next when an album runs out — the same shape a playlist's similar entities arrive in.

| Field | Type | Required | Notes |
|---|---|---|---|
| `items` | `list<SimilarEntityItem>` | no | list of [SimilarEntityItem](#similarentityitem) |

### AlbumTrailer

An album's trailer: the album, who made it, and what the trailer plays.

| Field | Type | Required | Notes |
|---|---|---|---|
| `album` | `?Album` | no | [Album](#album) |
| `artists` | `list<Artist>` | no | list of [Artist](#artist) |
| `trailer` | `?TrailerInfo` | no | [TrailerInfo](#trailerinfo) |

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

### AboutArtist

The artist's own page: who they are, what is written about them, and where else to find them.

| Field | Type | Required | Notes |
|---|---|---|---|
| `artist` | `?Artist` | no | [Artist](#artist) |
| `stats` | `?Stats` | no | [Stats](#stats) |
| `description` | `?string` | no |  |
| `links` | `list<ArtistLink>` | no | list of [ArtistLink](#artistlink) |
| `covers` | `list<Cover>` | no | list of [Cover](#cover) |
| `artistType` | `?string` | no |  |

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
| `derivedColors` | `?CoverDerivedColors` | no | [CoverDerivedColors](#coverderivedcolors) |
| `trailer` | `?Trailer` | no | [Trailer](#trailer) |
| `hasTrailer` | `?bool` | no |  |
| `extraActions` | `list<mixed>` | no |  |

### ArtistAlbums

A page of an artist's albums.

| Field | Type | Required | Notes |
|---|---|---|---|
| `albums` | `list<Album>` | no | list of [Album](#album) |
| `pager` | `?Pager` | no | [Pager](#pager) |

### ArtistClipData

A clip and who is in it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `clip` | `?Clip` | no | [Clip](#clip) |
| `artists` | `list<Artist>` | no | list of [Artist](#artist) |

### ArtistClipItem

One entry in an artist's clips, wrapped the way the landing wraps its own — a type beside the thing itself.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |
| `data` | `?ArtistClipData` | no |  |

### ArtistClips

A page of an artist's clips.

| Field | Type | Required | Notes |
|---|---|---|---|
| `items` | `list<ArtistClipItem>` | no | list of [ArtistClipItem](#artistclipitem) |
| `pager` | `?Pager` | no | [Pager](#pager) |

### ArtistDonationData

Where to send an artist money, and what for.

| Field | Type | Required | Notes |
|---|---|---|---|
| `tipUrl` | `?string` | no |  |
| `artist` | `?Artist` | no | [Artist](#artist) |
| `goal` | `?ArtistDonationGoal` | no | [ArtistDonationGoal](#artistdonationgoal) |

### ArtistDonationGoal

What an artist is collecting for.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `?string` | no |  |

### ArtistDonationItem

One entry in an artist's donation block, wrapped as a type beside its data.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |
| `data` | `?ArtistDonationData` | no |  |

### ArtistDonations

The ways an artist can be supported.

| Field | Type | Required | Notes |
|---|---|---|---|
| `donations` | `list<ArtistDonationItem>` | no | list of [ArtistDonationItem](#artistdonationitem) |

### ArtistInfo

An artist with the numbers around them, without the albums and tracks that make brief-info heavy.

| Field | Type | Required | Notes |
|---|---|---|---|
| `artist` | `?Artist` | no | [Artist](#artist) |
| `likesCount` | `?int` | no |  |
| `stats` | `?Stats` | no | [Stats](#stats) |
| `trailer` | `?ArtistTrailerStatus` | no | [ArtistTrailerStatus](#artisttrailerstatus) |
| `covers` | `list<Cover>` | no | list of [Cover](#cover) |
| `description` | `?string` | no |  |
| `artistType` | `?string` | no |  |

### ArtistLink

A link an artist put on their page, with something to show for it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `?string` | no |  |
| `subtitle` | `?string` | no |  |
| `url` | `?string` | no |  |
| `imgUrl` | `?string` | no |  |

### ArtistLinks

Everywhere else an artist can be found.

| Field | Type | Required | Notes |
|---|---|---|---|
| `links` | `list<ArtistLink>` | no | list of [ArtistLink](#artistlink) |

### ArtistSkeleton

The layout of an artist's page: which blocks to draw and where each one's contents come from.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?string` | no |  |
| `title` | `?string` | no |  |
| `blocks` | `list<SkeletonBlock>` | no | list of [SkeletonBlock](#skeletonblock) |

### ArtistTracks

A page of an artist's tracks.

| Field | Type | Required | Notes |
|---|---|---|---|
| `tracks` | `list<Track>` | no | list of [Track](#track) |
| `pager` | `?Pager` | no | [Pager](#pager) |

### ArtistTrailer

An artist's trailer and the tracks it plays.

| Field | Type | Required | Notes |
|---|---|---|---|
| `artist` | `?Artist` | no | [Artist](#artist) |
| `trailer` | `?TrailerInfo` | no | [TrailerInfo](#trailerinfo) |

### ArtistTrailerStatus

Whether an artist has a trailer to play.

| Field | Type | Required | Notes |
|---|---|---|---|
| `available` | `?bool` | no |  |

### BriefInfo

Everything the service will say about an artist in one response.

| Field | Type | Required | Notes |
|---|---|---|---|
| `artist` | `?Artist` | no | [Artist](#artist) |
| `albums` | `list<Album>` | no | list of [Album](#album) |
| `alsoAlbums` | `list<Album>` | no | list of [Album](#album) |
| `lastReleases` | `list<Album>` | no | list of [Album](#album) |
| `popularTracks` | `list<Track>` | no | list of [Track](#track) |
| `similarArtists` | `list<Artist>` | no | list of [Artist](#artist) |
| `allCovers` | `list<Cover>` | no | list of [Cover](#cover) |
| `videos` | `list<VideoSupplement>` | no | list of [VideoSupplement](#videosupplement) |
| `tracksInChart` | `list<Chart>` | no | list of [Chart](#chart) |
| `stats` | `?Stats` | no | [Stats](#stats) |
| `customWave` | `?CustomWave` | no | [CustomWave](#customwave) |
| `hasPromotions` | `?bool` | no |  |
| `hasTrailer` | `?bool` | no |  |
| `lastReleaseIds` | `list<int>` | no |  |
| `playlists` | `list<Playlist>` | no | list of [Playlist](#playlist) |
| `playlistIds` | `list<PlaylistId>` | no | list of [PlaylistId](#playlistid) |
| `concerts` | `list<mixed>` | no |  |
| `clips` | `list<Clip>` | no | list of [Clip](#clip) |
| `vinyls` | `list<Vinyl>` | no | list of [Vinyl](#vinyl) |
| `links` | `list<mixed>` | no | Promotional links — a different shape from the artist's own `links`, despite the name: these carry a subtitle and an image. |
| `bandlinkScannerLink` | `array<string,` | no |  |
| `extraActions` | `list<mixed>` | no |  |

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

### SimilarArtists

Who else sounds like a given artist.

| Field | Type | Required | Notes |
|---|---|---|---|
| `artist` | `?Artist` | no | [Artist](#artist) |
| `similarArtists` | `list<Artist>` | no | list of [Artist](#artist) |

### Stats

How many people listened to an artist lately, and whether that is rising.

| Field | Type | Required | Notes |
|---|---|---|---|
| `lastMonthListeners` | `int` | **yes** |  |
| `lastMonthListenersDelta` | `int` | **yes** |  |

### Vinyl

A record for sale, as brief-info offers it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `url` | `?string` | no |  |
| `title` | `?string` | no |  |
| `year` | `?int` | no |  |
| `price` | `?int` | no |  |
| `media` | `?string` | no |  |
| `offerId` | `?int` | no |  |
| `artistIds` | `list<int>` | no |  |
| `picture` | `?string` | no |  |

## Clip

### Clip

A short video for a track.

| Field | Type | Required | Notes |
|---|---|---|---|
| `clipId` | `?int` | no |  |
| `id` | `mixed` | no | Sent alongside clipId, and not always the same value. |
| `title` | `?string` | no |  |
| `version` | `?string` | no |  |
| `playerId` | `?string` | no |  |
| `uuid` | `?string` | no |  |
| `thumbnail` | `?string` | no |  |
| `previewUrl` | `?string` | no |  |
| `duration` | `?int` | no |  |
| `trackIds` | `list<int>` | no |  |
| `artists` | `list<Artist>` | no | list of [Artist](#artist) |
| `disclaimers` | `list<string>` | no |  |
| `explicit` | `?bool` | no |  |
| `cover` | `?Cover` | no | [Cover](#cover) |
| `contentRestrictions` | `?ContentRestrictions` | no | [ContentRestrictions](#contentrestrictions) |

### ClipsWillLike

A page of clips: the ones liked, or the ones suggested.

| Field | Type | Required | Notes |
|---|---|---|---|
| `clips` | `list<Clip>` | no | list of [Clip](#clip) |
| `pager` | `?Pager` | no | [Pager](#pager) |

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

## Feed

### AlbumEvent

An album the feed has something to say about, with the tracks it suggests from it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `album` | `?Album` | no | [Album](#album) |
| `tracks` | `list<Track>` | no | list of [Track](#track) |

### ArtistEvent

An artist the feed is recommending, why, and what to play.

| Field | Type | Required | Notes |
|---|---|---|---|
| `artist` | `?Artist` | no | [Artist](#artist) |
| `tracks` | `list<Track>` | no | list of [Track](#track) |
| `similarToArtistsFromHistory` | `list<Artist>` | no | list of [Artist](#artist) |
| `subscribed` | `?bool` | no |  |

### Day

One day of the feed.

| Field | Type | Required | Notes |
|---|---|---|---|
| `day` | `?string` | no |  |
| `events` | `list<Event>` | no | list of [Event](#event) |
| `tracksToPlayWithAds` | `list<TrackWithAds>` | no | list of [TrackWithAds](#trackwithads) |
| `tracksToPlay` | `list<Track>` | no | list of [Track](#track) |

### Event

One thing the feed has to say on a given day.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?string` | no |  |
| `type` | `?string` | no |  |
| `typeForFrom` | `?string` | no |  |
| `title` | `?string` | no |  |
| `tracks` | `list<Track>` | no | list of [Track](#track) |
| `artists` | `list<ArtistEvent>` | no | list of [ArtistEvent](#artistevent) |
| `albums` | `list<AlbumEvent>` | no | list of [AlbumEvent](#albumevent) |
| `message` | `?string` | no |  |
| `device` | `?string` | no |  |
| `tracksCount` | `?int` | no |  |
| `genre` | `?string` | no |  |
| `socialTracks` | `list<SocialTrack>` | no | list of [SocialTrack](#socialtrack) |

### Feed

The account's feed: what the service made for it, and what happened on which day.

| Field | Type | Required | Notes |
|---|---|---|---|
| `canGetMoreEvents` | `?bool` | no |  |
| `pumpkin` | `?bool` | no |  |
| `isWizardPassed` | `?bool` | no |  |
| `generatedPlaylists` | `list<GeneratedPlaylist>` | no | list of [GeneratedPlaylist](#generatedplaylist) |
| `headlines` | `list<string>` | no |  |
| `today` | `?string` | no |  |
| `days` | `list<Day>` | no | list of [Day](#day) |
| `nextRevision` | `?string` | no |  |

### SocialTrack

A track the feed is showing because people you follow liked it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `track` | `?Track` | no | [Track](#track) |
| `likedByUsers` | `list<User>` | no | list of [User](#user) |

### TrackWithAds

A track in a day's playback queue, or the slot where an advertisement goes.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |
| `track` | `?Track` | no | [Track](#track) |

## Genre

### Genre

A genre, and the genres inside it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?string` | no |  |
| `title` | `?string` | no |  |
| `fullTitle` | `?string` | no |  |
| `titles` | `array<string,` | no |  |
| `weight` | `?int` | no |  |
| `composerTop` | `?bool` | no |  |
| `showInMenu` | `?bool` | no |  |
| `showInRegions` | `list<int>` | no |  |
| `hideInRegions` | `list<int>` | no |  |
| `urlPart` | `?string` | no |  |
| `color` | `?string` | no |  |
| `images` | `?Images` | no | [Images](#images) |
| `radioIcon` | `?Icon` | no | [Icon](#icon) |
| `subGenres` | `list<self>` | no | list of [Genre](#genre) |

### Images

A genre's artwork, in the two sizes the service offers.

| Field | Type | Required | Notes |
|---|---|---|---|
| `_20x20` | `?string` | no |  |
| `_208x208` | `?string` | no |  |
| `_300x300` | `?string` | no |  |

### Title

A genre's name in one language.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `string` | **yes** |  |
| `fullTitle` | `?string` | no |  |

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

## Landing

### Block

One block of the front page: a heading and the things under it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?string` | no |  |
| `type` | `?string` | no |  |
| `typeForFrom` | `?string` | no |  |
| `title` | `?string` | no |  |
| `entities` | `list<BlockEntity>` | no | list of [BlockEntity](#blockentity) |
| `description` | `?string` | no |  |
| `data` | `mixed` | no |  |
| `playContext` | `?PlaylistId` | no | [PlaylistId](#playlistid). The playlist this block plays when it is played as a whole. |
| `backgroundImageUrl` | `?string` | no |  |
| `backgroundVideoUrl` | `?string` | no |  |
| `backgroundVideoId` | `?string` | no |  |

### BlockEntity

One item inside a block of the front page.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?string` | no |  |
| `type` | `?string` | no |  |
| `data` | `mixed` | no |  |

### Chart

A track's standing in a chart, and which way it is moving.

| Field | Type | Required | Notes |
|---|---|---|---|
| `position` | `int` | **yes** |  |
| `progress` | `string` | **yes** |  |
| `listeners` | `int` | **yes** |  |
| `shift` | `int` | **yes** | Places gained or lost since the last reckoning. |
| `bgColor` | `?string` | no |  |
| `trackId` | `?TrackId` | no | [TrackId](#trackid) |

### ChartInfo

A chart, which the service models as a playlist whose tracks carry their standing, plus the menu of other charts.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?string` | no |  |
| `type` | `?string` | no |  |
| `typeForFrom` | `?string` | no |  |
| `title` | `?string` | no |  |
| `menu` | `?ChartInfoMenu` | no | [ChartInfoMenu](#chartinfomenu) |
| `chart` | `?Playlist` | no | [Playlist](#playlist) |
| `chartDescription` | `?string` | no |  |

### ChartInfoMenu

The charts on offer beside the one being shown.

| Field | Type | Required | Notes |
|---|---|---|---|
| `items` | `list<ChartInfoMenuItem>` | no | list of [ChartInfoMenuItem](#chartinfomenuitem) |

### ChartInfoMenuItem

One choice of chart — a country, or a kind of music.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `?string` | no |  |
| `url` | `?string` | no |  |
| `selected` | `?bool` | no |  |

### ChartItem

A track in a chart, with its standing.

| Field | Type | Required | Notes |
|---|---|---|---|
| `track` | `?Track` | no | [Track](#track) |
| `chart` | `?Chart` | no | [Chart](#chart) |

### Landing

The front page: whichever blocks were asked for, in the order to show them.

| Field | Type | Required | Notes |
|---|---|---|---|
| `pumpkin` | `?bool` | no |  |
| `contentId` | `mixed` | no |  |
| `blocks` | `list<Block>` | no | list of [Block](#block) |

### LandingList

A page of one kind of thing — new releases, new playlists or podcasts.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |
| `typeForFrom` | `?string` | no |  |
| `title` | `?string` | no |  |
| `id` | `?string` | no |  |
| `newReleases` | `list<int>` | no |  |
| `newPlaylists` | `list<PlaylistId>` | no | list of [PlaylistId](#playlistid) |
| `podcasts` | `list<int>` | no |  |

### MixLink

A link to a mix — one of the coloured tiles on the front page.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `?string` | no |  |
| `url` | `?string` | no |  |
| `urlScheme` | `?string` | no |  |
| `textColor` | `?string` | no |  |
| `backgroundColor` | `?string` | no |  |
| `backgroundImageUri` | `?string` | no |  |
| `coverWhite` | `?string` | no |  |
| `coverUri` | `?string` | no |  |

### PersonalPlaylistsData

What the personal-playlists block knows beyond its entities: whether the account has answered the taste wizard, which decides how good they are.

| Field | Type | Required | Notes |
|---|---|---|---|
| `isWizardPassed` | `?bool` | no |  |

### PlayContext

Somewhere the account was listening, so it can be picked up again.

| Field | Type | Required | Notes |
|---|---|---|---|
| `playedIn` | `?string` | no | The client it was played in; `client` on the wire, renamed to leave that name to the back-reference. |
| `context` | `?string` | no |  |
| `contextItem` | `?string` | no |  |
| `tracks` | `list<TrackShortOld>` | no | list of [TrackShortOld](#trackshortold) |
| `payload` | `mixed` | no | What was being listened to: whichever kind $context names. |

### PlayContextsData

The tracks a play-contexts block offers beyond its entities.

| Field | Type | Required | Notes |
|---|---|---|---|
| `otherTracks` | `list<TrackShortOld>` | no | list of [TrackShortOld](#trackshortold) |

### Promotion

A promoted something on the front page, with everything needed to draw it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `promoId` | `?string` | no |  |
| `title` | `?string` | no |  |
| `subtitle` | `?string` | no |  |
| `heading` | `?string` | no |  |
| `url` | `?string` | no |  |
| `urlScheme` | `?string` | no |  |
| `textColor` | `?string` | no |  |
| `gradient` | `?string` | no |  |
| `image` | `?string` | no |  |

### TrackId

A reference to a track rather than the track itself.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?int` | no |  |
| `trackId` | `?int` | no |  |
| `albumId` | `?int` | no |  |
| `from` | `?string` | no |  |

### TrackShortOld

A track reference as the landing sends it: the pair of ids and when it was played, without the track.

| Field | Type | Required | Notes |
|---|---|---|---|
| `trackId` | `?TrackId` | no | [TrackId](#trackid) |
| `timestamp` | `?string` | no |  |

## Playlist

### Brand

A sponsor's dressing for a playlist: artwork, colors and the tracking pixels that come with paid placement.

| Field | Type | Required | Notes |
|---|---|---|---|
| `image` | `string` | **yes** |  |
| `background` | `string` | **yes** |  |
| `reference` | `string` | **yes** |  |
| `pixels` | `list<string>` | **yes** |  |
| `theme` | `string` | **yes** |  |
| `playlistTheme` | `string` | **yes** |  |
| `button` | `string` | **yes** |  |

### CaseForms

A name in all six Russian cases.

| Field | Type | Required | Notes |
|---|---|---|---|
| `nominative` | `string` | **yes** |  |
| `genitive` | `string` | **yes** |  |
| `dative` | `string` | **yes** |  |
| `accusative` | `string` | **yes** |  |
| `instrumental` | `string` | **yes** |  |
| `prepositional` | `string` | **yes** |  |

### Contest

A playlist's entry in a Yandex.Music contest.

| Field | Type | Required | Notes |
|---|---|---|---|
| `contestId` | `string` | **yes** |  |
| `status` | `string` | **yes** |  |
| `canEdit` | `bool` | **yes** |  |
| `sent` | `?string` | no |  |
| `withdrawn` | `?string` | no |  |

### GeneratedPlaylist

A playlist the service generated for the account — the daily playlist, the weekly release digest, and their kin.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |
| `ready` | `?bool` | no |  |
| `notify` | `?bool` | no |  |
| `data` | `?Playlist` | no | [Playlist](#playlist) |
| `description` | `list<mixed>` | no |  |
| `previewDescription` | `?string` | no |  |

### MadeFor

Who a generated playlist was made for.

| Field | Type | Required | Notes |
|---|---|---|---|
| `userInfo` | `?User` | no | [User](#user) |
| `caseForms` | `?CaseForms` | no | [CaseForms](#caseforms) |

### MadeForUser

Whether a playlist was generated for the account reading it, and that person's name in every case so a heading can be built around it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `isMadeForUser` | `?bool` | no |  |
| `caseForms` | `?CaseForms` | no | [CaseForms](#caseforms) |

### OpenGraphData

What a link to this playlist unfurls into when it is shared.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `string` | **yes** |  |
| `description` | `string` | **yes** |  |
| `image` | `?Cover` | no | [Cover](#cover) |

### PlayCounter

How many days in a row the owner has listened to a daily playlist, and the phrase the app shows for it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `value` | `int` | **yes** |  |
| `description` | `string` | **yes** |  |
| `updated` | `bool` | **yes** |  |

### Playlist

A playlist.

| Field | Type | Required | Notes |
|---|---|---|---|
| `uid` | `?int` | no |  |
| `kind` | `?int` | no |  |
| `title` | `?string` | no |  |
| `owner` | `?User` | no | [User](#user) |
| `cover` | `?Cover` | no | [Cover](#cover) |
| `trackCount` | `?int` | no |  |
| `tracks` | `list<TrackShort>` | no | list of [TrackShort](#trackshort) |
| `revision` | `?int` | no |  |
| `snapshot` | `?int` | no |  |
| `visibility` | `?string` | no |  |
| `collective` | `?bool` | no |  |
| `urlPart` | `?string` | no |  |
| `created` | `?string` | no |  |
| `modified` | `?string` | no |  |
| `available` | `?bool` | no |  |
| `isBanner` | `?bool` | no |  |
| `isPremiere` | `?bool` | no |  |
| `durationMs` | `?int` | no |  |
| `likesCount` | `?int` | no |  |
| `description` | `?string` | no |  |
| `descriptionFormatted` | `?string` | no |  |
| `playlistUuid` | `?string` | no |  |
| `type` | `?string` | no |  |
| `ready` | `?bool` | no |  |
| `everPlayed` | `?bool` | no |  |
| `generatedPlaylistType` | `?string` | no |  |
| `madeFor` | `?MadeFor` | no | [MadeFor](#madefor) |
| `madeForUser` | `?MadeForUser` | no | [MadeForUser](#madeforuser) |
| `derivedColors` | `?CoverDerivedColors` | no | [CoverDerivedColors](#coverderivedcolors) |
| `playCounter` | `?PlayCounter` | no | [PlayCounter](#playcounter) |
| `playlistAbsence` | `?PlaylistAbsence` | no | [PlaylistAbsence](#playlistabsence) |
| `contest` | `?Contest` | no | [Contest](#contest) |
| `branding` | `?Brand` | no | [Brand](#brand) |
| `ogData` | `?OpenGraphData` | no | [OpenGraphData](#opengraphdata) |
| `ogImage` | `?string` | no |  |
| `ogTitle` | `?string` | no |  |
| `ogDescription` | `?string` | no |  |
| `image` | `?string` | no |  |
| `coverWithoutText` | `?Cover` | no | [Cover](#cover) |
| `animatedCoverUri` | `?string` | no |  |
| `backgroundColor` | `?string` | no |  |
| `textColor` | `?string` | no |  |
| `backgroundImageUrl` | `?string` | no |  |
| `backgroundVideoUrl` | `?string` | no |  |
| `backgroundVideoId` | `?string` | no |  |
| `idForFrom` | `?string` | no |  |
| `dummyDescription` | `?string` | no |  |
| `dummyPageDescription` | `?string` | no |  |
| `dummyCover` | `?Cover` | no | [Cover](#cover) |
| `dummyRolloverCover` | `?Cover` | no | [Cover](#cover) |
| `metrikaId` | `?int` | no |  |
| `coauthors` | `list<int>` | no |  |
| `topArtist` | `list<Artist>` | no | list of [Artist](#artist) |
| `recentTracks` | `list<TrackId>` | no | list of [TrackId](#trackid) |
| `similarPlaylists` | `list<self>` | no | list of [Playlist](#playlist) |
| `lastOwnerPlaylists` | `list<self>` | no | list of [Playlist](#playlist) |
| `customWave` | `?CustomWave` | no | [CustomWave](#customwave) |
| `pager` | `?Pager` | no | [Pager](#pager) |
| `hasTrailer` | `?bool` | no |  |
| `actionButton` | `?ActionButton` | no | [ActionButton](#actionbutton) |
| `trailer` | `?PlaylistAvailability` | no | [PlaylistAvailability](#playlistavailability) |
| `tags` | `list<mixed>` | no |  |
| `prerolls` | `list<mixed>` | no |  |
| `regions` | `list<mixed>` | no |  |
| `isForFrom` | `mixed` | no | Not modelled: the reference types it as Any, and live responses vary. |

### PlaylistAbsence

Why a playlist that was asked for is not there.

| Field | Type | Required | Notes |
|---|---|---|---|
| `kind` | `int` | **yes** |  |
| `reason` | `string` | **yes** |  |

### PlaylistAvailability

Whether a playlist's trailer can be played.

| Field | Type | Required | Notes |
|---|---|---|---|
| `available` | `?bool` | no |  |

### PlaylistId

A reference to a playlist rather than the playlist itself: its owner and its kind, which is what identifies one.

| Field | Type | Required | Notes |
|---|---|---|---|
| `uid` | `?int` | no |  |
| `kind` | `?int` | no |  |
| `playlistUuid` | `?string` | no | Sent where a reference doubles as a link, such as a landing block. |

### PlaylistRecommendations

Tracks the service suggests adding to a playlist.

| Field | Type | Required | Notes |
|---|---|---|---|
| `tracks` | `list<Track>` | no | list of [Track](#track) |
| `batchId` | `?string` | no |  |

### PlaylistSimilarEntities

Things to listen to next when this playlist runs out.

| Field | Type | Required | Notes |
|---|---|---|---|
| `items` | `list<SimilarEntityItem>` | no | list of [SimilarEntityItem](#similarentityitem) |

### PlaylistTrailer

A playlist's trailer: the playlist itself, the tracks the trailer plays, and whether it may be shared.

| Field | Type | Required | Notes |
|---|---|---|---|
| `playlist` | `?Playlist` | no | [Playlist](#playlist) |
| `trailer` | `?TrailerInfo` | no | [TrailerInfo](#trailerinfo) |
| `shareable` | `?bool` | no |  |

### PlaylistsList

The envelope /playlists answers with.

| Field | Type | Required | Notes |
|---|---|---|---|
| `playlists` | `list<Playlist>` | no | list of [Playlist](#playlist) |

### TagResult

The playlists filed under a tag, as references rather than playlists.

| Field | Type | Required | Notes |
|---|---|---|---|
| `tag` | `?string` | no |  |
| `ids` | `list<PlaylistId>` | no | list of [PlaylistId](#playlistid) |

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

### AdParams

What an advertisement inserted into a station needs in order to be requested and reported.

| Field | Type | Required | Notes |
|---|---|---|---|
| `partnerId` | `mixed` | no |  |
| `categoryId` | `mixed` | no |  |
| `pageRef` | `?string` | no |  |
| `targetRef` | `?string` | no |  |
| `otherParams` | `?string` | no |  |
| `adVolume` | `?int` | no |  |
| `genreId` | `?string` | no |  |
| `genreName` | `?string` | no |  |

### Dashboard

The stations offered to this account, in the order they should be shown.

| Field | Type | Required | Notes |
|---|---|---|---|
| `dashboardId` | `?string` | no |  |
| `stations` | `list<StationResult>` | no | list of [StationResult](#stationresult) |
| `pumpkin` | `?bool` | no |  |

### DiscreteScale

A setting that slides between two ends rather than picking from a list.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `string` | **yes** |  |
| `name` | `string` | **yes** |  |
| `min` | `?Value` | no | [Value](#value) |
| `max` | `?Value` | no | [Value](#value) |

### Enum

A setting that takes one of a listed set of values.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `string` | **yes** |  |
| `name` | `string` | **yes** |  |
| `possibleValues` | `list<Value>` | no | list of [Value](#value) |

### Id

What identifies a station: a kind and a tag, such as `genre` and `allrock`.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `string` | **yes** |  |
| `tag` | `string` | **yes** |  |

### Restrictions

What a station can be tuned to.

| Field | Type | Required | Notes |
|---|---|---|---|
| `language` | `?Enum` | no | [Enum](#enum) |
| `diversity` | `?Enum` | no | [Enum](#enum) |
| `mood` | `?DiscreteScale` | no | [DiscreteScale](#discretescale) |
| `energy` | `?DiscreteScale` | no | [DiscreteScale](#discretescale) |
| `moodEnergy` | `?Enum` | no | [Enum](#enum) |

### RotorSettings

How a station is currently tuned.

| Field | Type | Required | Notes |
|---|---|---|---|
| `language` | `?string` | no |  |
| `diversity` | `?string` | no |  |
| `mood` | `?int` | no |  |
| `energy` | `?int` | no |  |
| `moodEnergy` | `?string` | no |  |

### Sequence

One item in what a station is about to play.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |
| `track` | `?Track` | no | [Track](#track) |
| `liked` | `?bool` | no |  |
| `trackParameters` | `?TrackParameters` | no | [TrackParameters](#trackparameters) |

### Station

A radio station: what it is called, how it looks, and what it can be tuned to.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?Id` | no | [Id](#id) |
| `name` | `?string` | no |  |
| `icon` | `?Icon` | no | [Icon](#icon) |
| `mtsIcon` | `?Icon` | no | [Icon](#icon) |
| `geocellIcon` | `?Icon` | no | [Icon](#icon) |
| `idForFrom` | `?string` | no |  |
| `restrictions` | `?Restrictions` | no | [Restrictions](#restrictions) |
| `restrictions2` | `?Restrictions` | no | [Restrictions](#restrictions). The same restrictions in a newer arrangement, without the scales. |
| `fullImageUrl` | `?string` | no |  |
| `mtsFullImageUrl` | `?string` | no |  |
| `parentId` | `?Id` | no | [Id](#id) |

### StationData

The personal radio station attached to an account.

| Field | Type | Required | Notes |
|---|---|---|---|
| `name` | `string` | **yes** |  |

### StationResult

A station together with how it is tuned right now.

| Field | Type | Required | Notes |
|---|---|---|---|
| `station` | `?Station` | no | [Station](#station) |
| `settings` | `?RotorSettings` | no | [RotorSettings](#rotorsettings) |
| `settings2` | `?RotorSettings` | no | [RotorSettings](#rotorsettings) |
| `adParams` | `?AdParams` | no | [AdParams](#adparams) |
| `explanation` | `?string` | no |  |
| `prerolls` | `list<mixed>` | no |  |
| `rupTitle` | `?string` | no |  |
| `rupDescription` | `?string` | no |  |
| `customName` | `?string` | no |  |

### StationTracksResult

What a station will play next.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?Id` | no | [Id](#id) |
| `sequence` | `list<Sequence>` | no | list of [Sequence](#sequence) |
| `batchId` | `?string` | no |  |
| `pumpkin` | `?bool` | no |  |
| `radioSessionId` | `?string` | no | Identifies the listening session the feedback belongs to. |

### TrackParameters

How a track sounds, as the station's own analysis measures it: tempo, a hue to paint it with, and how energetic it is.

| Field | Type | Required | Notes |
|---|---|---|---|
| `bpm` | `?int` | no |  |
| `hue` | `?int` | no |  |
| `energy` | `?float` | no |  |

### Value

One allowed setting: what to send, and what to call it in an interface.

| Field | Type | Required | Notes |
|---|---|---|---|
| `value` | `string` | **yes** |  |
| `name` | `string` | **yes** |  |
| `imageUrl` | `?string` | no | The three below arrive only in `restrictions2`, the newer arrangement: artwork for the value, whether it stands for "not chosen", and the seed that selects it elsewhere in the service. |
| `unspecified` | `?bool` | no |  |
| `serializedSeed` | `?string` | no |  |

## Search

### Best

The single best match for a query, whatever kind of thing that turned out to be.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |
| `result` | `mixed` | no |  |
| `text` | `?string` | no |  |

### Search

What a search found.

| Field | Type | Required | Notes |
|---|---|---|---|
| `searchRequestId` | `?string` | no |  |
| `text` | `?string` | no |  |
| `best` | `?Best` | no | [Best](#best) |
| `albums` | `SearchResult<Album>|null` | no |  |
| `artists` | `SearchResult<Artist>|null` | no |  |
| `playlists` | `SearchResult<Playlist>|null` | no |  |
| `tracks` | `SearchResult<Track>|null` | no |  |
| `videos` | `SearchResult<Video>|null` | no |  |
| `clips` | `SearchResult<Clip>|null` | no | Clips, which arrive without a type of their own — the only set that relies on the field name to say what it holds. |
| `users` | `SearchResult<User>|null` | no |  |
| `podcasts` | `SearchResult<Album>|null` | no |  |
| `podcastEpisodes` | `SearchResult<Track>|null` | no |  |
| `type` | `?string` | no |  |
| `page` | `?int` | no |  |
| `perPage` | `?int` | no |  |
| `misspellResult` | `?string` | no |  |
| `misspellOriginal` | `?string` | no |  |
| `misspellCorrected` | `?bool` | no |  |
| `nocorrect` | `?bool` | no |  |

### SearchResult

One kind of thing a search found, and where in the whole of it this page sits.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |
| `total` | `?int` | no |  |
| `perPage` | `?int` | no |  |
| `order` | `?int` | no |  |
| `results` | `array` | no |  |

### Suggestions

What to offer someone who has typed part of a query.

| Field | Type | Required | Notes |
|---|---|---|---|
| `best` | `?Best` | no | [Best](#best) |
| `suggestions` | `list<string>` | no |  |

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

## Skeleton

### SkeletonBlock

One block of a page laid out by the service.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?string` | no |  |
| `type` | `?string` | no |  |
| `data` | `?SkeletonBlockData` | no | [SkeletonBlockData](#skeletonblockdata) |

### SkeletonBlockData

What a block of a page holds: its tabs, where its contents come from, and how to see the rest.

| Field | Type | Required | Notes |
|---|---|---|---|
| `tabs` | `list<SkeletonTab>` | no | list of [SkeletonTab](#skeletontab) |
| `selectedTabIndex` | `?int` | no |  |
| `source` | `?SkeletonSource` | no | [SkeletonSource](#skeletonsource) |
| `title` | `?string` | no |  |
| `showPolicy` | `?string` | no |  |
| `viewAllAction` | `?SkeletonViewAllAction` | no | [SkeletonViewAllAction](#skeletonviewallaction) |

### SkeletonSource

Where a block's contents come from, and how much of it there is.

| Field | Type | Required | Notes |
|---|---|---|---|
| `uri` | `?string` | no |  |
| `count` | `?int` | no |  |
| `countWeb` | `?int` | no |  |

### SkeletonTab

A tab of a block, holding blocks of its own.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `?string` | no |  |
| `title` | `?string` | no |  |
| `blocks` | `list<SkeletonBlock>` | no | list of [SkeletonBlock](#skeletonblock) |

### SkeletonViewAllAction

Where "see all" leads, in an app and on the web.

| Field | Type | Required | Notes |
|---|---|---|---|
| `deeplink` | `?string` | no |  |
| `weblink` | `?string` | no |  |

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

### ActionButton

A call to action shown on an album or a playlist, such as a pre-save prompt or a link to a promotion.

| Field | Type | Required | Notes |
|---|---|---|---|
| `text` | `?string` | no |  |
| `url` | `?string` | no |  |
| `color` | `?string` | no |  |

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

### CustomWave

The personal radio station offered for an artist or album, and how to present it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `?string` | no |  |
| `animationUrl` | `?string` | no |  |
| `header` | `?string` | no |  |
| `backgroundImageUrl` | `?string` | no |  |
| `position` | `?string` | no | Where the offer sits on the page, such as `bottom`. |
| `squareAgentAnimation` | `?string` | no |  |
| `imageUrl` | `?string` | no |  |

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

### Icon

A station's artwork: a picture and the color to show behind it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `backgroundColor` | `?string` | no |  |
| `imageUrl` | `?string` | no |  |

### Like

One entry in a user's likes: what was liked, and when.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |
| `id` | `mixed` | no |  |
| `timestamp` | `?string` | no |  |
| `album` | `?Album` | no | [Album](#album) |
| `artist` | `?Artist` | no | [Artist](#artist) |
| `playlist` | `?Playlist` | no | [Playlist](#playlist) |
| `shortDescription` | `?string` | no |  |
| `description` | `?string` | no |  |
| `isPremiere` | `?bool` | no |  |
| `isBanner` | `?bool` | no |  |

### Pager

Where a paged response sits in the whole of what there is.

| Field | Type | Required | Notes |
|---|---|---|---|
| `total` | `int` | **yes** |  |
| `page` | `int` | **yes** |  |
| `perPage` | `int` | **yes** |  |

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

### TracksList

A user's library of liked or disliked tracks.

| Field | Type | Required | Notes |
|---|---|---|---|
| `uid` | `?int` | no |  |
| `revision` | `?int` | no |  |
| `playlistUuid` | `?string` | no | The library is itself a playlist; this is its uuid. |
| `tracks` | `list<TrackShort>` | no | list of [TrackShort](#trackshort) |

### Trailer

Whether a trailer exists for something.

| Field | Type | Required | Notes |
|---|---|---|---|
| `available` | `?bool` | no |  |

### TrailerInfo

The tracks a playlist's trailer is built from.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `?string` | no |  |
| `tracks` | `list<Track>` | no | list of [Track](#track) |

### Video

A music video, as search returns it.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `?string` | no |  |
| `cover` | `?string` | no |  |
| `embedUrl` | `?string` | no |  |
| `provider` | `?string` | no |  |
| `providerVideoId` | `mixed` | no |  |
| `youtubeUrl` | `?string` | no |  |
| `thumbnailUrl` | `?string` | no |  |
| `duration` | `?int` | no |  |
| `text` | `?string` | no |  |
| `htmlAutoPlayVideoPlayer` | `?string` | no |  |
| `regions` | `list<string>` | no |  |

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
| `substituted` | `?Track` | no | [Track](#track). What plays instead, where the original is unavailable here. |
| `matchedTrack` | `?Track` | no | [Track](#track) |
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
| `podcastEpisodeType` | `?string` | no | A podcast episode's kind — `full` or `trailer` on everything seen so far. |
| `pubDate` | `?string` | no | When the episode was published, as `YYYY-MM-DD`. |
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
| `chart` | `?Chart` | no | [Chart](#chart). Where the track currently sits in a chart, when it is in one. |

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

### TrackShort

A track's place in a playlist rather than the track itself.

| Field | Type | Required | Notes |
|---|---|---|---|
| `id` | `mixed` | **yes** |  |
| `timestamp` | `?string` | no |  |
| `albumId` | `mixed` | no |  |
| `playCount` | `?int` | no |  |
| `recent` | `?bool` | no |  |
| `chart` | `?Chart` | no | [Chart](#chart) |
| `track` | `?Track` | no | [Track](#track) |
| `originalIndex` | `?int` | no |  |

### TrackTrailer

A trailer introducing a track or episode.

| Field | Type | Required | Notes |
|---|---|---|---|
| `title` | `?string` | no |  |
| `track` | `?Track` | no | [Track](#track) |

## Wave

### SimilarEntityData

What a similar entity actually is, once its type has said which half of this to read.

| Field | Type | Required | Notes |
|---|---|---|---|
| `wave` | `?Wave` | no | [Wave](#wave) |
| `agent` | `?WaveAgent` | no | [WaveAgent](#waveagent) |
| `album` | `?Album` | no | [Album](#album). An album's similar entities point at albums rather than at waves. |
| `artist` | `?Artist` | no | [Artist](#artist) |
| `artists` | `list<Artist>` | no | list of [Artist](#artist) |

### SimilarEntityItem

One entry in a list of things like the one you asked about.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |
| `data` | `?SimilarEntityData` | no | [SimilarEntityData](#similarentitydata) |

### Wave

A personal radio station — what it plays and what it was seeded from.

| Field | Type | Required | Notes |
|---|---|---|---|
| `name` | `?string` | no |  |
| `description` | `?string` | no |  |
| `seeds` | `list<string>` | no |  |

### WaveAgent

The face a wave is presented under.

| Field | Type | Required | Notes |
|---|---|---|---|
| `animationUri` | `?string` | no |  |
| `cover` | `?Cover` | no | [Cover](#cover) |
| `entity` | `?WaveAgentEntity` | no | [WaveAgentEntity](#waveagententity) |

### WaveAgentEntity

What a wave agent stands for.

| Field | Type | Required | Notes |
|---|---|---|---|
| `type` | `?string` | no |  |

