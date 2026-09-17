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
| `usersSettings()` | `GET /users/{userId}/settings` | `?UserSettings` | A user's playback settings. |

## Albums

| Method | Request | Returns | Notes |
|---|---|---|---|
| `album()` | `GET /albums/{albumId}` | `?Album` | One album, without its tracks. |
| `albums()` | `POST /albums` | `array` | Fetch albums by id. |
| `albumsDisclaimer()` | `GET /albums/{albumId}/disclaimer` | `array` | Notices that must accompany an album. |
| `albumsWithTracks()` | `GET /albums/{albumId}/with-tracks` | `?Album` | One album with everything on it. |

## Artists

| Method | Request | Returns | Notes |
|---|---|---|---|
| `artists()` | `POST /artists` | `array` | Fetch artists by id. |
| `artistsAlsoAlbums()` | — | `?ArtistAlbums` | A page of the albums an artist appears on without being their author — compilations, guest spots. |
| `artistsBriefInfo()` | `GET /artists/{artistId}/brief-info` | `?BriefInfo` | Everything the service will say about an artist at once — albums, popular tracks, similar artists, covers, chart positions. |
| `artistsDirectAlbums()` | — | `?ArtistAlbums` | A page of the albums an artist made. |
| `artistsSimilar()` | `GET /artists/{artistId}/similar` | `?SimilarArtists` | Who else sounds like this artist. |
| `artistsTrackIdsByRating()` | `GET /artists/{artistId}/track-ids-by-rating` | `array` | The artist's tracks as bare ids, ordered by rating. |
| `artistsTracks()` | `GET /artists/{artistId}/tracks` | `?ArtistTracks` | A page of an artist's tracks, most popular first. |

## Clips

| Method | Request | Returns | Notes |
|---|---|---|---|
| `clips()` | `GET /clips` | `array` | Fetch clips by id. |
| `clipsWillLike()` | `GET /clips/will/like` | `?ClipsWillLike` | A page of clips the service thinks the account will like. |

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
| `feed()` | `GET /feed` | `array` |  |
| `feedWizardIsPassed()` | `GET /feed/wizard/is-passed` | `mixed` |  |
| `genres()` | `GET /genres` | `array` |  |
| `landing()` | `GET /landing3` | `array` | Blocks understood by the endpoint: personalplaylists, promotions, new-releases, new-playlists, mixes, chart, artists, albums, playlists, play_contexts. |
| `rotorAccountStatus()` | `GET /rotor/account/status` | `?Status` | The account as radio sees it — the same model, with a few extra fields filled in such as how many skips per hour are left. |
| `rotorStationGenreFeedback()` | `POST {url}` | `mixed` |  |
| `rotorStationGenreFeedbackRadioStarted()` | — | `mixed` |  |
| `rotorStationGenreFeedbackTrackStarted()` | — | `mixed` |  |
| `rotorStationGenreInfo()` | `GET /rotor/station/genre:{genre}/info` | `mixed` |  |
| `rotorStationGenreTracks()` | `GET /rotor/station/genre:{genre}/tracks` | `mixed` |  |
| `rotorStationsDashboard()` | `GET /rotor/stations/dashboard` | `array` |  |
| `rotorStationsList()` | `GET /rotor/stations/list` | `mixed` |  |

## Likes

| Method | Request | Returns | Notes |
|---|---|---|---|
| `usersDislikesArtists()` | `GET /users/{userId}/dislikes/artists` | `array` | The account's disliked artists. |
| `usersDislikesArtistsAdd()` | — | `bool` |  |
| `usersDislikesArtistsRemove()` | — | `bool` |  |
| `usersDislikesTracks()` | `GET /users/{userId}/dislikes/tracks` | `?TracksList` | The account's disliked tracks. |
| `usersDislikesTracksAdd()` | — | `bool` |  |
| `usersDislikesTracksRemove()` | — | `bool` |  |
| `usersLikesAlbums()` | — | `array` | The account's liked albums. |
| `usersLikesAlbumsAdd()` | — | `bool` |  |
| `usersLikesAlbumsRemove()` | — | `bool` |  |
| `usersLikesArtists()` | — | `array` | The account's liked artists. |
| `usersLikesArtistsAdd()` | — | `bool` |  |
| `usersLikesArtistsRemove()` | — | `bool` |  |
| `usersLikesClips()` | `GET /users/{userId}/likes/clips` | `?ClipsWillLike` | A page of the account's liked clips. |
| `usersLikesClipsAdd()` | — | `bool` |  |
| `usersLikesClipsRemove()` | — | `bool` |  |
| `usersLikesPlaylists()` | — | `array` | The account's liked playlists. |
| `usersLikesPlaylistsAdd()` | — | `bool` | Like playlists, identified as `{uid}:{kind}`. |
| `usersLikesPlaylistsRemove()` | — | `bool` |  |
| `usersLikesTracks()` | `GET /users/{userId}/likes/tracks` | `?TracksList` | The account's liked tracks. |
| `usersLikesTracksAdd()` | — | `bool` |  |
| `usersLikesTracksRemove()` | — | `bool` |  |

## Playlists

| Method | Request | Returns | Notes |
|---|---|---|---|
| `playlist()` | `GET /playlist/{playlistUuid}` | `?Playlist` | A playlist by its uuid rather than by owner and kind. |
| `playlistSimilarEntities()` | `GET /playlist/{playlistUuid}/similar-entities` | `?PlaylistSimilarEntities` | What to listen to next when a playlist runs out. |
| `playlists()` | `GET /playlists` | `?PlaylistsList` | Fetch playlists by owner-and-kind pairs, as `{uid}:{kind}`. |
| `playlistsCollectiveJoin()` | `POST {url}` | `bool` | Join a collective playlist with an invitation token. |
| `playlistsList()` | `POST /playlists/list` | `array` | Fetch playlists by owner-and-kind pairs, as `{uid}:{kind}`. |
| `playlistsPersonal()` | `GET /playlists/personal/{playlistId}` | `?GeneratedPlaylist` | One of the playlists the service generates for the account, such as the daily playlist. |
| `usersPlaylists()` | `GET /users/{userId}/playlists/{kind}` | `?Playlist` | One playlist of a user's. |
| `usersPlaylistsChange()` | `POST /users/{userId}/playlists/{kind}/change` | `?Playlist` | Apply a set of changes to a playlist's contents. |
| `usersPlaylistsCreate()` | `POST /users/{userId}/playlists/create` | `?Playlist` | Create a playlist. |
| `usersPlaylistsDelete()` | `POST /users/{userId}/playlists/{kind}/delete` | `bool` | Delete a playlist. |
| `usersPlaylistsDeleteTrack()` | — | `?Playlist` | Remove the tracks in a range of positions: from $from up to but not including $to. |
| `usersPlaylistsDescription()` | — | `?Playlist` | Set a playlist's description. |
| `usersPlaylistsInsertTrack()` | — | `?Playlist` | Insert tracks at a position in a playlist. |
| `usersPlaylistsKinds()` | `GET /users/{userId}/playlists/list/kinds` | `array` | The kinds of a user's playlists and nothing else. |
| `usersPlaylistsList()` | `GET /users/{userId}/playlists/list` | `array` | Every playlist a user has. |
| `usersPlaylistsMany()` | `POST /users/{userId}/playlists` | `array` | Several playlists of one user's, in one request. |
| `usersPlaylistsName()` | — | `?Playlist` | Rename a playlist. |
| `usersPlaylistsRecommendations()` | `GET /users/{userId}/playlists/{kind}/recommendations` | `?PlaylistRecommendations` | Tracks the service suggests adding to a playlist. |
| `usersPlaylistsTrailer()` | `GET /users/{userId}/playlists/{kind}/trailer` | `?PlaylistTrailer` | A playlist's trailer, and the tracks it is built from. |
| `usersPlaylistsVisibility()` | — | `?Playlist` | Make a playlist public or private. |

## Search

| Method | Request | Returns | Notes |
|---|---|---|---|
| `search()` | `GET /search` | `?Search` | Search for something. |
| `searchSuggest()` | `GET /search/suggest` | `?Suggestions` | What to offer for a partly typed query. |

## Tracks

| Method | Request | Returns | Notes |
|---|---|---|---|
| `afterTrack()` | `GET /after-track` | `?ShotEvent` | What the service wants played between two tracks — one of Alice's spoken interjections, typically. |
| `playAudio()` | `POST /play-audio` | `bool` | Report that a track was played. |
| `trackSupplement()` | `GET /tracks/{trackId}/supplement` | `?Supplement` | Videos and, for podcasts, the full description. |
| `tracks()` | `POST /tracks` | `array` | Fetch tracks by id. |
| `tracksCredits()` | `GET /tracks/{trackId}/credits` | `?Credits` |  |
| `tracksDisclaimer()` | `GET /tracks/{trackId}/disclaimer` | `array` | Notices that must accompany a track. |
| `tracksDownloadInfo()` | `GET /tracks/{trackId}/download-info` | `array` | The ways a track can be downloaded. |
| `tracksFullInfo()` | `GET /tracks/{trackId}/full-info` | `?TrackFullInfo` |  |
| `tracksLyrics()` | `GET /tracks/{trackId}/lyrics` | `?TrackLyrics` | Where to fetch a track's lyrics. |
| `tracksSimilar()` | `GET /tracks/{trackId}/similar` | `?SimilarTracks` |  |
| `tracksTrailer()` | `GET /tracks/{trackId}/trailer` | `?TrackTrailer` |  |

