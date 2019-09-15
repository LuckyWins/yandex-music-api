<?php

include 'config.php';

class Yandex {

	//playlist.jsx?owner=Night.Jaguar.Saint&kinds=3&light=true&madeFor=&lang=ru&external-domain=music.yandex.ru&overembed=false&ncrnd=0.779022775473756
	public static function getPlaylist($username, $playlistId) {
		global $config;
		$url = $config['host'].'/handlers/playlist.jsx?'
			.'owner='.$username
			.'&kinds='.$playlistId;
		return json_decode(file_get_contents($url));
	}

	//album.jsx?album=8316120&lang=ru&external-domain=music.yandex.ru&overembed=false&ncrnd=0.4769895392108956
	public static function getAlbum($albumId) {
		global $config;
		$url = $config['host'].'/handlers/album.jsx?'
			.'album='.$albumId;
		return json_decode(file_get_contents($url));
	}

	//track.jsx?track=56266797%3A8316120&lang=ru&external-domain=music.yandex.ru&overembed=false&ncrnd=0.6632941415156501
	public static function getTrack($trackId) {
		global $config;
		$url = $config['host'].'/handlers/track.jsx?'
			.'track='.$trackId;
		return json_decode(file_get_contents($url));
	}

	public static function getTrackLinks($storageDir) {
		global $config;
		header('set-cookie: yandexuid=8675993201568368604; path=/; domain=.yandex.ru; expires=Thu, 13 Sep 2029 09:56:44 GMT; max-age=315360000');
		header('X-Retpath-Y: https%3A%2F%2Fmusic.yandex.ru%2Fusers%2FNight.Jaguar.Saint%2Fplaylists%2F3');
		$url = 'https://music.yandex.ru/api/v2.1/handlers/track/34237165:4207045/web-own_playlists-playlist-track-main/download/m';
		return json_decode(file_get_contents($url));
		//return json_decode(file_get_contents($url));
	}

}

?>