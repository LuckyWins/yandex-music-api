# Endpoints

Generated from the source by `make docs` — do not edit.

Methods still returning raw decoded arrays live in `Legacy` and move into a
typed trait as each domain is ported.

A dash in the request column means the method issues no request of its own —
it delegates to another one. Braces mark the parts of a path the caller
supplies.

## Account

| Method | Request | Returns | Notes |
|---|---|---|---|
| `accountExperiments()` | `GET /account/experiments` | `array` | The A/B experiments the account is in, as a bare map. |
| `accountExperimentsDetails()` | `GET /account/experiments/details` | `?ExperimentsDetails` |  |
| `accountSettings()` | `GET /account/settings` | `?UserSettings` |  |
| `accountSettingsSet()` | — | `?UserSettings` | Change one setting. |
| `accountSettingsSetMany()` | `POST /account/settings` | `?UserSettings` | Change several settings at once. |
| `accountStatus()` | `GET /account/status` | `?Status` |  |
| `consumePromoCode()` | `POST /account/consume-promo-code` | `?PromoCodeStatus` | Redeem a promo code. |
| `getAccountUid()` | — | `?int` | The user id every per-user endpoint needs. |
| `init()` | — | `self` | Load the account for the current token. |
| `me()` | — | `?Status` | The account status loaded by init(), or null if it has not run. |
| `permissionAlerts()` | `GET /permission-alerts` | `?PermissionAlerts` |  |
| `settings()` | `GET /settings` | `?Settings` | What the account can be sold, and where to buy it. |

## DeviceAuth

| Method | Request | Returns | Notes |
|---|---|---|---|
| `deviceAuth()` | — | `OAuthToken` | The whole flow: request a code, hand it to the caller to display, then poll until the user confirms. |
| `pollDeviceToken()` | `POST /token` | `?OAuthToken` | Step two, asked repeatedly: has the user confirmed yet? Returns null while the user has not answered — that is the normal state for most of the flow, not a failure. |
| `requestDeviceCode()` | `POST /device/code` | `DeviceCode` | Step one: ask for a code for the user to confirm. |
| `revokeToken()` | `POST /revoke_token` | `void` | Revoke a token, so that it stops working immediately. |

## Legacy

| Method | Request | Returns | Notes |
|---|---|---|---|
| `albums()` | — | `mixed` |  |
| `albumsWithTracks()` | `GET /albums/{albumId}/with-tracks` | `array` |  |
| `artists()` | — | `mixed` |  |
| `artistsBriefInfo()` | `GET /artists/{artistId}/brief-info` | `array` |  |
| `feed()` | `GET /feed` | `array` |  |
| `feedWizardIsPassed()` | `GET /feed/wizard/is-passed` | `mixed` |  |
| `genres()` | `GET /genres` | `array` |  |
| `getDirectLink()` | — | `never` | Turn a download-info URL into a direct link to the audio. |
| `getLikesAlbums()` | — | `mixed` |  |
| `getLikesArtists()` | — | `mixed` |  |
| `getLikesPlaylists()` | — | `mixed` |  |
| `getLikesTracks()` | — | `mixed` |  |
| `landing()` | `GET /landing3` | `array` | Blocks understood by the endpoint: personalplaylists, promotions, new-releases, new-playlists, mixes, chart, artists, albums, playlists, play_contexts. |
| `playlistsList()` | — | `mixed` |  |
| `rotorAccountStatus()` | `GET /rotor/account/status` | `?Status` | The account as radio sees it — the same model, with a few extra fields filled in such as how many skips per hour are left. |
| `rotorStationGenreFeedback()` | `POST {url}` | `mixed` |  |
| `rotorStationGenreFeedbackRadioStarted()` | — | `mixed` |  |
| `rotorStationGenreFeedbackTrackStarted()` | — | `mixed` |  |
| `rotorStationGenreInfo()` | `GET /rotor/station/genre:{genre}/info` | `mixed` |  |
| `rotorStationGenreTracks()` | `GET /rotor/station/genre:{genre}/tracks` | `mixed` |  |
| `rotorStationsDashboard()` | `GET /rotor/stations/dashboard` | `array` |  |
| `rotorStationsList()` | `GET /rotor/stations/list` | `mixed` |  |
| `search()` | `GET /search` | `array` |  |
| `searchSuggest()` | `GET /search/suggest` | `array` |  |
| `tracks()` | — | `mixed` |  |
| `tracksDownloadInfo()` | `GET /tracks/{trackId}/download-info` | `mixed` | Download variants for a track. |
| `usersDislikesTracks()` | `GET /users/{accountUid}/dislikes/tracks` | `mixed` |  |
| `usersDislikesTracksAdd()` | — | `mixed` |  |
| `usersDislikesTracksRemove()` | — | `mixed` |  |
| `usersLikesAlbumsAdd()` | — | `mixed` |  |
| `usersLikesAlbumsRemove()` | — | `mixed` |  |
| `usersLikesArtistsAdd()` | — | `mixed` |  |
| `usersLikesArtistsRemove()` | — | `mixed` |  |
| `usersLikesPlaylistsAdd()` | — | `mixed` |  |
| `usersLikesPlaylistsRemove()` | — | `mixed` |  |
| `usersLikesTracksAdd()` | — | `mixed` |  |
| `usersLikesTracksRemove()` | — | `mixed` |  |
| `usersPlaylists()` | `POST /users/{userId}/playlists` | `mixed` |  |
| `usersPlaylistsCreate()` | `POST /users/{accountUid}/playlists/create` | `array` |  |
| `usersPlaylistsDelete()` | `POST /users/{accountUid}/playlists/{kind}/delete` | `mixed` |  |
| `usersPlaylistsInsertTrack()` | — | `mixed` | Insert a track at a position in a playlist. |
| `usersPlaylistsList()` | `GET /users/{accountUid}/playlists/list` | `array` |  |
| `usersPlaylistsNameChange()` | `POST /users/{accountUid}/playlists/{kind}/name` | `mixed` |  |

