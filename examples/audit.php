<?php

declare(strict_types=1);

/**
 * Walk every reading endpoint and report what the models do not know about.
 *
 * The library claims parity with the reference and claims every field is
 * typed. `tools/compare-with-reference.php` checks the first claim. This
 * checks the second: it calls each endpoint with unknown-field reporting on,
 * and prints every field the API sent that no model declares.
 *
 * Three rules it keeps to.
 *
 * It does not write. Only reading endpoints are called; the ones that change
 * the account are listed below with a reason, and that list is part of the
 * report rather than a silent omission. The writing endpoints are exercised by
 * the domain probes, which clean up after themselves.
 *
 * It does not print values. Names and types only — the responses are about
 * your account, and the point of this report is that it is safe to paste into
 * an issue.
 *
 * It does not hardcode identifiers. A dead id is accepted in silence by this
 * API and looks exactly like a broken method, so everything is found by
 * following the API: a search gives an artist, the artist gives albums, the
 * albums give tracks and labels, and so on.
 *
 * Pass a search query as the first argument to start somewhere else.
 */

require __DIR__.'/../vendor/autoload.php';

use LuckyWins\YandexMusic\Examples\Support\Bootstrap;
use LuckyWins\YandexMusic\Examples\Support\Sweep;
use LuckyWins\YandexMusic\Examples\Support\UnknownFieldCollector;
use LuckyWins\YandexMusic\Model\Album\Album;
use LuckyWins\YandexMusic\Model\Artist\Artist;
use LuckyWins\YandexMusic\Model\Clip\Clip;
use LuckyWins\YandexMusic\Model\Label\Label;
use LuckyWins\YandexMusic\Model\Landing\BlockType;
use LuckyWins\YandexMusic\Model\Model;
use LuckyWins\YandexMusic\Model\MusicHistory\MusicHistoryQuery;
use LuckyWins\YandexMusic\Model\Playlist\Playlist;
use LuckyWins\YandexMusic\Model\Search\SearchResult;
use LuckyWins\YandexMusic\Model\Track\Track;

$arguments = Bootstrap::arguments();
$query = $arguments[1] ?? 'radiohead';

// Ends quietly rather than failing: `make audit` runs this after the reference
// check, and having no token is a reason to skip it, not an error.
if (null === Bootstrap::token()) {
    echo "No token stored, so the live sweep is skipped.\n";
    echo "Run `php examples/device_auth.php` to authorize, then run this again.\n";

    exit(0);
}

/**
 * The first item of a list that is what we hoped it would be.
 *
 * Several endpoints answer with mixed lists — a search result set holds
 * whatever the service filed under it, which is not always what the field name
 * says — so the type has to be checked rather than assumed.
 *
 * @template T of object
 *
 * @param array<int, mixed> $items
 * @param class-string<T>   $class
 *
 * @return T|null
 */
function firstOf(array $items, string $class): ?object
{
    foreach ($items as $item) {
        if ($item instanceof $class) {
            return $item;
        }
    }

    return null;
}

/**
 * What a search filed under one heading, or nothing when it filed nothing.
 *
 * @template T of Model
 *
 * @param SearchResult<T>|null $set
 *
 * @return list<T>
 */
function resultsOf(?SearchResult $set): array
{
    return null === $set ? [] : $set->results;
}

$unknownFields = new UnknownFieldCollector();
$client = Bootstrap::authorizedClient($unknownFields);
$sweep = new Sweep($unknownFields);

// Everything this sweep will not call, and why in a word. `writes` changes the
// account; `auth` would take or destroy a token.
$sweep->skipAll([
    'accountSettingsSet' => 'writes',
    'accountSettingsSetMany' => 'writes',
    'consumePromoCode' => 'writes',
    'requestDeviceCode' => 'auth',
    'pollDeviceToken' => 'auth',
    'deviceAuth' => 'auth',
    'revokeToken' => 'auth',
    'usersLikesTracksAdd' => 'writes',
    'usersLikesTracksRemove' => 'writes',
    'usersLikesAlbumsAdd' => 'writes',
    'usersLikesAlbumsRemove' => 'writes',
    'usersLikesArtistsAdd' => 'writes',
    'usersLikesArtistsRemove' => 'writes',
    'usersLikesPlaylistsAdd' => 'writes',
    'usersLikesPlaylistsRemove' => 'writes',
    'usersLikesClipsAdd' => 'writes',
    'usersLikesClipsRemove' => 'writes',
    'usersDislikesTracksAdd' => 'writes',
    'usersDislikesTracksRemove' => 'writes',
    'usersDislikesArtistsAdd' => 'writes',
    'usersDislikesArtistsRemove' => 'writes',
    'usersPlaylistsCreate' => 'writes',
    'usersPlaylistsDelete' => 'writes',
    'usersPlaylistsName' => 'writes',
    'usersPlaylistsVisibility' => 'writes',
    'usersPlaylistsDescription' => 'writes',
    'usersPlaylistsChange' => 'writes',
    'usersPlaylistsInsertTrack' => 'writes',
    'usersPlaylistsDeleteTrack' => 'writes',
    'playlistsCollectiveJoin' => 'writes',
    'pinAlbum' => 'writes',
    'unpinAlbum' => 'writes',
    'pinArtist' => 'writes',
    'unpinArtist' => 'writes',
    'pinPlaylist' => 'writes',
    'unpinPlaylist' => 'writes',
    'pinWave' => 'writes',
    'unpinWave' => 'writes',
    'usersPresavesAdd' => 'writes',
    'usersPresavesRemove' => 'writes',
    'queueCreate' => 'writes',
    'queueUpdatePosition' => 'writes',
    'rotorStationSettings' => 'writes',
    'rotorStationFeedback' => 'writes',
    'rotorStationFeedbackRadioStarted' => 'writes',
    'rotorStationFeedbackTrackStarted' => 'writes',
    'rotorStationFeedbackTrackFinished' => 'writes',
    'rotorStationFeedbackSkip' => 'writes',
    'playAudio' => 'writes',
    'afterTrack' => 'writes',
]);

echo "== the account ==\n";

$sweep->run('init', static fn () => $client->init());
$sweep->run('getAccountUid', static fn () => $client->getAccountUid());
$sweep->run('accountStatus', static fn () => $client->accountStatus());
$sweep->run('me', static fn () => $client->me());
$sweep->run('accountSettings', static fn () => $client->accountSettings());
$sweep->run('usersSettings', static fn () => $client->usersSettings());
$sweep->run('settings', static fn () => $client->settings());
$sweep->run('permissionAlerts', static fn () => $client->permissionAlerts());
$sweep->run('accountExperiments', static fn () => $client->accountExperiments());
$sweep->run('accountExperimentsDetails', static fn () => $client->accountExperimentsDetails());

echo "\n== the front page ==\n";

// Every block type at once: each one carries a different payload, and the
// unusual ones are exactly where a field goes unnoticed.
$sweep->run('landing', static fn () => $client->landing(BlockType::cases()));
$chart = $sweep->run('chart', static fn () => $client->chart());
$sweep->run('newReleases', static fn () => $client->newReleases());
$sweep->run('newPlaylists', static fn () => $client->newPlaylists());
$sweep->run('podcasts', static fn () => $client->podcasts());
$genres = $sweep->run('genres', static fn () => $client->genres());
$feed = $sweep->run('feed', static fn () => $client->feed());
$sweep->run('feedWizardIsPassed', static fn () => $client->feedWizardIsPassed());

$genreId = ($genres[0] ?? null)?->id;

if (null !== $genreId) {
    $sweep->run('tags', static fn () => $client->tags($genreId));
}

echo "\n== clips ==\n";

$clips = $sweep->run('clipsWillLike', static fn () => $client->clipsWillLike(0, 5));
$clip = firstOf(null === $clips ? [] : $clips->clips, Clip::class);
$clipId = $clip?->clipId;

if (null !== $clipId) {
    $sweep->run('clips', static fn () => $client->clips($clipId));
    $sweep->run('clipsCredits', static fn () => $client->clipsCredits($clipId));
    $sweep->run('clipsDisclaimer', static fn () => $client->clipsDisclaimer($clipId));
}

echo "\n== the catalogue, found by searching for \"{$query}\" ==\n";

$search = $sweep->run('search', static fn () => $client->search($query));
$sweep->run('searchSuggest', static fn () => $client->searchSuggest(mb_substr($query, 0, 4)));

$foundTrack = firstOf(resultsOf($search?->tracks), Track::class);
$foundAlbum = firstOf(resultsOf($search?->albums), Album::class);
$foundArtist = firstOf(resultsOf($search?->artists), Artist::class);
$foundPlaylist = firstOf(resultsOf($search?->playlists), Playlist::class);

// The chart is a second source of a track, for a search that found none.
$chartEntry = ($chart?->chart?->tracks[0] ?? null)?->track;
$track = $foundTrack ?? $chartEntry;
$album = $foundAlbum ?? firstOf(null === $track ? [] : $track->albums, Album::class);
$artist = $foundArtist ?? firstOf(null === $track ? [] : $track->artists, Artist::class);

$trackId = $track?->id;
$albumId = $album?->id;
$artistId = $artist?->id;

printf(
    "  starting from track %s, album %s, artist %s\n",
    $trackId ?? '—',
    $albumId ?? '—',
    $artistId ?? '—',
);

if (null !== $trackId) {
    $sweep->run('tracks', static fn () => $client->tracks($trackId));
    $sweep->run('tracksDownloadInfo', static fn () => $client->tracksDownloadInfo($trackId));
    $sweep->run('trackSupplement', static fn () => $client->trackSupplement($trackId));
    $sweep->run('tracksLyrics', static fn () => $client->tracksLyrics($trackId));
    $sweep->run('tracksSimilar', static fn () => $client->tracksSimilar($trackId));
    $sweep->run('tracksTrailer', static fn () => $client->tracksTrailer($trackId));
    $sweep->run('tracksFullInfo', static fn () => $client->tracksFullInfo($trackId));
    $sweep->run('tracksCredits', static fn () => $client->tracksCredits($trackId));
    $sweep->run('tracksDisclaimer', static fn () => $client->tracksDisclaimer($trackId));
}

$fullAlbum = null;

if (null !== $albumId) {
    $sweep->run('albums', static fn () => $client->albums($albumId));
    $fullAlbum = $sweep->run('album', static fn () => $client->album($albumId));
    $sweep->run('albumsWithTracks', static fn () => $client->albumsWithTracks($albumId));
    $sweep->run('albumsDisclaimer', static fn () => $client->albumsDisclaimer($albumId));
    $sweep->run('albumsTrailer', static fn () => $client->albumsTrailer($albumId));
    $sweep->run('albumsSimilarEntities', static fn () => $client->albumsSimilarEntities($albumId));
}

if (null !== $artistId) {
    $sweep->run('artists', static fn () => $client->artists($artistId));
    $sweep->run('artistsBriefInfo', static fn () => $client->artistsBriefInfo($artistId));
    $sweep->run('artistsInfo', static fn () => $client->artistsInfo($artistId));
    $sweep->run('artistsAbout', static fn () => $client->artistsAbout($artistId));
    $sweep->run('artistsLinks', static fn () => $client->artistsLinks($artistId));
    $sweep->run('artistsTracks', static fn () => $client->artistsTracks($artistId, 0, 5));
    $sweep->run('artistsTrackIds', static fn () => $client->artistsTrackIds($artistId));
    $sweep->run('artistsTrackIdsByRating', static fn () => $client->artistsTrackIdsByRating($artistId));
    $sweep->run('artistsDirectAlbums', static fn () => $client->artistsDirectAlbums($artistId, 0, 5));
    $sweep->run('artistsAlsoAlbums', static fn () => $client->artistsAlsoAlbums($artistId, 0, 5));
    $sweep->run('artistsDiscographyAlbums', static fn () => $client->artistsDiscographyAlbums($artistId, 0, 5));
    $sweep->run('artistsSafeDirectAlbums', static fn () => $client->artistsSafeDirectAlbums($artistId, 0, 5));
    $sweep->run('artistsSimilar', static fn () => $client->artistsSimilar($artistId));
    // Not the artist the rest of this block is about: an artist with no clips
    // answers 404, which exercises nothing. The clip feed names artists that
    // certainly have one.
    $clipArtistId = firstOf(null === $clip ? [] : $clip->artists, Artist::class)->id ?? $artistId;

    $sweep->run('artistsClips', static fn () => $client->artistsClips($clipArtistId));
    $sweep->run('artistsConcerts', static fn () => $client->artistsConcerts($artistId));
    $sweep->run('artistsDonation', static fn () => $client->artistsDonation($artistId));
    $sweep->run('artistsTrailer', static fn () => $client->artistsTrailer($artistId));
    $sweep->run('artistsDisclaimer', static fn () => $client->artistsDisclaimer($artistId));
    $sweep->run('artistsSkeleton', static fn () => $client->artistsSkeleton($artistId, 'artist-page'));
}

echo "\n== labels, reached through the album ==\n";

$label = firstOf(null === $fullAlbum ? [] : $fullAlbum->labels, Label::class);

if (null === $label) {
    echo "  the album named no label to follow\n";
} else {
    $labelId = $label->id;

    $sweep->run('label', static fn () => $client->label($labelId));
    $sweep->run('labelAlbums', static fn () => $client->labelAlbums($labelId, 0, 5));
    $sweep->run('labelArtists', static fn () => $client->labelArtists($labelId, 0, 5));
}

echo "\n== playlists ==\n";

$mine = $sweep->run('usersPlaylistsList', static fn () => $client->usersPlaylistsList());
$kinds = $sweep->run('usersPlaylistsKinds', static fn () => $client->usersPlaylistsKinds());
$kind = $kinds[0] ?? null;

if (null !== $kind) {
    $sweep->run('usersPlaylists', static fn () => $client->usersPlaylists($kind));
    $sweep->run('usersPlaylistsMany', static fn () => $client->usersPlaylistsMany([$kind]));
    $sweep->run('usersPlaylistsRecommendations', static fn () => $client->usersPlaylistsRecommendations($kind));
    $sweep->run('usersPlaylistsTrailer', static fn () => $client->usersPlaylistsTrailer($kind));
}

// Someone else's playlist rather than the account's own: they are shaped
// differently, and a public one carries fields a private one never sends.
$public = $foundPlaylist ?? firstOf(null === $mine ? [] : $mine, Playlist::class);
$uuid = $public?->playlistUuid;
$uid = $public?->uid;
$publicKind = $public?->kind;

if (null !== $uuid) {
    $sweep->run('playlist', static fn () => $client->playlist($uuid));
}

// Similar entities are not offered for every playlist: the chart and some
// public ones answer `playlist-not-found` while the account's own answer
// normally. So ask about one of ours, and fall back to whatever was found.
$ownUuid = firstOf(null === $mine ? [] : $mine, Playlist::class)->playlistUuid ?? $uuid;

if (null !== $ownUuid) {
    $sweep->run('playlistSimilarEntities', static fn () => $client->playlistSimilarEntities($ownUuid));
}

if (null !== $uid && null !== $publicKind) {
    // These two take uid:kind pairs, not the uuid the single-playlist
    // endpoints want — checked against the live API, where uuids are refused.
    $pair = $uid.':'.$publicKind;

    $sweep->run('playlists', static fn () => $client->playlists($pair));
    $sweep->run('playlistsList', static fn () => $client->playlistsList($pair));
}

$generated = (null === $feed ? [] : $feed->generatedPlaylists)[0] ?? null;
$generatedType = $generated?->type;

if (null !== $generatedType) {
    $sweep->run('playlistsPersonal', static fn () => $client->playlistsPersonal($generatedType));
}

echo "\n== what the account likes ==\n";

$sweep->run('usersLikesTracks', static fn () => $client->usersLikesTracks());
$sweep->run('usersLikesAlbums', static fn () => $client->usersLikesAlbums());
$sweep->run('usersLikesArtists', static fn () => $client->usersLikesArtists());
$sweep->run('usersLikesPlaylists', static fn () => $client->usersLikesPlaylists());
$sweep->run('usersLikesClips', static fn () => $client->usersLikesClips(0, 5));
$sweep->run('usersDislikesTracks', static fn () => $client->usersDislikesTracks());
$sweep->run('usersDislikesArtists', static fn () => $client->usersDislikesArtists());

echo "\n== radio ==\n";

$sweep->run('rotorAccountStatus', static fn () => $client->rotorAccountStatus());
$sweep->run('rotorStationsDashboard', static fn () => $client->rotorStationsDashboard());
$stations = $sweep->run('rotorStationsList', static fn () => $client->rotorStationsList());
$stationId = ($stations[0] ?? null)?->station?->id;

if (null !== $stationId) {
    $sweep->run('rotorStationInfo', static fn () => $client->rotorStationInfo($stationId));
    $sweep->run('rotorStationTracks', static fn () => $client->rotorStationTracks($stationId));
}

echo "\n== pins, queues and presaves ==\n";

$sweep->run('pins', static fn () => $client->pins());
$sweep->run('usersPresaves', static fn () => $client->usersPresaves());

$queues = $sweep->run('queuesList', static fn () => $client->queuesList());
$queueId = ($queues[0] ?? null)?->id;

if (null === $queueId) {
    echo "  no queue on this device to open\n";
} else {
    $sweep->run('queue', static fn () => $client->queue($queueId));
}

echo "\n== tags and what was played ==\n";

$metatags = $sweep->run('metatags', static fn () => $client->metatags());
$metatag = (null === $metatags ? [] : $metatags->tags())[0] ?? null;
$metatagId = $metatag?->tag;

if (null !== $metatagId) {
    $sweep->run('metatag', static fn () => $client->metatag($metatagId, tracksCount: 3, albumsCount: 3));
    $sweep->run('metatagAlbums', static fn () => $client->metatagAlbums($metatagId, 0, 5));
    $sweep->run('metatagArtists', static fn () => $client->metatagArtists($metatagId, offset: 0, limit: 5));
    $sweep->run('metatagPlaylists', static fn () => $client->metatagPlaylists($metatagId, 0, 5));
}

$history = $sweep->run('musicHistory', static fn () => $client->musicHistory(5));
$historyQuery = new MusicHistoryQuery();

foreach (array_slice(null === $history ? [] : $history->historyTabs, 0, 2) as $day) {
    foreach (array_slice($day->items, 0, 2) as $group) {
        $played = ($group->tracks[0] ?? null)?->data?->itemId;

        if (null !== $played && null !== $played->trackId && null !== $played->albumId) {
            $historyQuery->track($played->trackId, $played->albumId);
        }
    }
}

if ($historyQuery->isEmpty()) {
    echo "  nothing in the history to ask about\n";
} else {
    $sweep->run('musicHistoryItems', static fn () => $client->musicHistoryItems($historyQuery));
}

echo "\n== concerts ==\n";

$sweep->run('concertsLocations', static fn () => $client->concertsLocations());
$sweep->run('concertsTabConfig', static fn () => $client->concertsTabConfig());
$concerts = $sweep->run('concertsFeed', static fn () => $client->concertsFeed());
$concertId = ($concerts?->concerts()[0] ?? null)?->id;

if (null === $concertId) {
    echo "  nothing on around here\n";
} else {
    $sweep->run('concertInfo', static fn () => $client->concertInfo($concertId));
    $sweep->run('concertSkeleton', static fn () => $client->concertSkeleton($concertId));
}

echo "\n".$unknownFields->summary();
echo "\n".$sweep->report();
