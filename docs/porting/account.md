# Account

Ported in the second stage. 25 models, nine client methods.

## Why this domain first

The first stage proved the foundation on flat models only — both device auth
models are scalars — so nested deserialization had never run against a real
response. `Status` reaches six levels down, through `Subscription`,
`AutoRenewable` and `Product` to `LicenceTextPart`, across four namespaces. It
was the right place to find out whether the model base actually worked.

It also unblocked eleven methods in `Legacy`: everything that acts on behalf of
a user needs the account id that `init()` loads.

## Methods

| Was | Became | In Python | Difference from the reference |
|---|---|---|---|
| `accountStatus(): array` | `accountStatus(): ?Status` | `account_status()` | none |
| `settings(): array` | `settings(): ?Settings` | `settings()` | none |
| `permissionAlert(): array` | `permissionAlerts(): ?PermissionAlerts` | `permission_alerts()` | ours was singular; renamed to match the endpoint |
| `accountExperiments(): array` | `accountExperiments(): array` | `account_experiments()` → `Experiments` | theirs is a class that copies whatever arrived into itself, with no schema and no tests; an array says the same thing honestly |
| `consumePromoCode()` | `consumePromoCode(): ?PromoCodeStatus` | `consume_promo_code()` | none |
| `init(): self` | `init(): self` | `init()` | none |
| `getAccount(): ?array` | `me(): ?Status` | the `client.me` field | a method here, a field there; returns the whole status rather than the nested account |
| private `accountUid()` | `getAccountUid(): ?int` | the `client.account_uid` field | a method here, a field there |
| `rotorAccountStatus(): array` | `rotorAccountStatus(): ?Status` | `rotor_account_status()` | theirs lives in the radio domain; ours stays in `Legacy` but is typed, since the model already exists |
| — | `accountSettings()` | `account_settings()` | none |
| — | `accountSettingsSet(string, scalar)` | `account_settings_set(param, value, data)` | theirs is one method with two mutually exclusive modes chosen by which arguments you passed; ours is two methods with honest signatures |
| — | `accountSettingsSetMany(array)` | the same method | the other mode |
| — | `accountExperimentsDetails()` | `account_experiments_details()` | none |
| `fromToken(string)` | **removed** | **absent** | ours. `setToken()` plus `init()`, and its only caller died with the password grant |
| `isTokenValid(string)` | **removed** | **absent** | ours. Checked for a 39-character token where current ones are 55, and nothing ever called it |

## What this domain changed in the model base

**Field names are matched ignoring case and separators.** Property names here
are the API's own camelCase, and the reference's snake_case cannot tell us
whether Yandex sends `lastFm` or `lastFM` — the conversion is lossy. A wrong
guess would have dropped the field in silence. Two properties that collide once
canonicalized are now refused when the class is first used.

**`ExperimentsDetails` deserializes the response body itself.** The endpoint
answers with the experiment map at the root rather than under a key, so it
cannot be declared through `NESTED`: there is no key to hang it on.

## Fields the reference does not know about

Running against the live API with `reportUnknownFields: true` turned up eleven
fields Yandex sends that the Python library does not model. Types were read off
real responses rather than guessed:

| Model | Field | Type |
|---|---|---|
| `Account` | `regionCode` | `string` |
| `Account` | `nonOwnerFamilyMember` | `bool` |
| `Status` | `masterhub` | object, shape undocumented |
| `Status` | `hasOptions` | list, contents undocumented |
| `UserSettings` | `explicitForbidden`, `childModEnabled`, `childModeChangedByUser`, `wizardIsPassed`, `aiContentReductionEnabled` | `bool` |
| `UserSettings` | `userCollectionHue` | `int` |
| `Product` | `offersPositionId` | `string` |
| `Settings` | `offersBatchId` | `string` |

`masterhub` and `hasOptions` are kept raw. Their shape is documented nowhere,
and inventing one would be worse than admitting we do not know it.

These are worth reporting upstream — the reference repository has an issue
template for exactly this.

## Traps found in the reference

- `Account`'s identity is conditional: with no `uid` there is nothing to key on,
  so two anonymous accounts are equal only when they are the same object
- `Product::$licenceTextParts` is annotated `List[Price]` there but filled with
  `LicenceTextPart`. The annotation is wrong; the content is what counts
- `Product::$features` is null when absent while its neighbour
  `$paymentMethodTypes` is empty. The distinction is deliberate — do not unify
- `Subscription` has two pairs of confusable names:
  `nonAutoRenewableRemainder` is a day count, `nonAutoRenewable` is a date
  window; `autoRenewable` and `familyAutoRenewable` hold the same model for
  different payers
- `Status::$barBelow` deserializes into `Alert` — the key does not match the
  class
- `Operator::$deactivation` and `Subscription::$operator` are singular names
  holding lists
- `settings` and `permission-alerts` sit at the root, not under `/account`

## Left for later

`usersSettings()` from the playlists domain returns the same `UserSettings`, but
wrapped in a `userSettings` key, so it is not a one-liner and it is not this
domain.

Noted while reading: the reference's `Playlist.is_mine` compares a string to an
int and is therefore always false. Do not reproduce it when playlists are ported.
