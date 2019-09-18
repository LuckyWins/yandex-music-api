<?php

include 'utils/request.php';

class Client {

    private $CLIENT_ID = '23cabbbdc6cd418abb4b39c32c41195d';
	private $CLIENT_SECRET = "53bc75238f0c4d08a118e51fe9203300";

	private $oauthUrl = "https://oauth.yandex.ru";
	private $baseUrl = "https://api.music.yandex.net";

	private $account;
	private $token;

	private $requestYandexAPI;

    /**
     * Получение информации о текущем аккаунте
     *
     * @return array
     */
    public function getAccount() {
        return $this->account;
    }

    /**
     * Client constructor.
     * @param string $token
     */
    public function __construct($token = "") {
        if ($token != "") {
            $this->token = $token;
            $this->requestYandexAPI = new RequestYandexAPI($token);
            $this->updateAccountStatus();
        }else{
            $this->requestYandexAPI = new RequestYandexAPI();
        }
    }

    /**
     * Инициализция клиента по токену
     * Ничем не отличается от Client($token). Так исторически сложилось.
     *
     * @param string $token Уникальный ключ для аутентификации
     */
    public function fromToken($token) {
        $this->token = $token;
        $this->requestYandexAPI->updateToken($token);
        $this->updateAccountStatus();
    }

    /**
     * Инициализация пользователя по паре логин/пароль
     * Рекомендуется сгенерировать его самостоятельно, сохранить и использовать
     * при инициализации клиента. Хранить логин/пароль - плохая идея!
     *
     * @param string $user Логин клиента
     * @param string $password Пароль клиента
     * @param bool $print Выводить на экран токен?
     */
	public function fromCredentials($user, $password, $print = false) {
	    $this->fromToken($this->generateTokenFromCredentials($user, $password, $print));
    }

    /**
     * Метод получения OAuth-токена по паре логин/пароль
     *
     * @param string $user Логин клиента
     * @param string $password Пароль клиента
     * @param bool $print Выводить на экран токен?
     * @param string $grantType Тип разрешения OAuth
     *
     * @return string OAuth-токен
     */
    private function generateTokenFromCredentials($user, $password, $print, $grantType = "password") {
        $url = $this->oauthUrl."/token";
        $data = array(
            'grant_type' => $grantType,
            'client_id' => $this->CLIENT_ID,
            'client_secret' => $this->CLIENT_SECRET,
            'username' => $user,
            'password' => $password
        );

        $token = json_decode($this->post($url,$data))->access_token;

        if ($print) {
            echo 'token: '.$token;
        }
        return $token;
    }

    /**
     * Примитивная валидация токена
     *
     * @param string $token OAuth-токен
     * @return bool Валидность токена
     */
    public function isTokenValid($token) {
        $token = trim(preg_replace('/\s+/', ' ', $token));
        if (strlen($token) != 39) {
            return false;
        }
        return true;
    }

    /**
     * Получение статуса аккаунта
     *
     * @return mixed decoded json
     */
    public function accountStatus() {
        $url = $this->baseUrl."/account/status";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * Обновление статуса аккаунта
     */
    private function updateAccountStatus() {
        $this->account = $this->accountStatus()->account;
        $this->requestYandexAPI->updateUser($this->account->login);
    }

    /**
     * Получение предложений по покупке
     *
     * @return mixed decoded json
     */
    public function settings() {
        $url = $this->baseUrl."/settings";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * Получение оповещений
     *
     * @return mixed decoded json
     */
    public function permissionAlert() {
        $url = $this->baseUrl."/permission-alerts";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * Получение значений экспериментальных функций аккаунта
     *
     * @return mixed decoded json
     */
    public function accountExperiments() {
        $url = $this->baseUrl."/account/experiments";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * Активация промо-кода
     *
     * @param string $code Промо-код
     * @param string $lang Язык ответа API в ISO 639-1
     *
     * @return mixed decoded json
     */
    public function consumePromoCode($code, $lang = 'en') {
        $url = $this->baseUrl."/account/consume-promo-code";

        $data = array(
            'code' => $code,
            'language' => $lang
        );

        $response = json_decode($this->post($url, $data))->result;

        return $response;
    }

    /**
     * Получение потока информации (фида) подобранного под пользователя.
     * Содержит умные плейлисты.
     *
     * @return mixed decoded json
     */
    public function feed() {
        $url = $this->baseUrl."/feed";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    public function feedWizardIsPassed() {
        $url = $this->baseUrl."/feed/wizard/is-passed";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * Получение лендинг-страницы содержащий блоки с новыми релизами,
     * чартами, плейлистами с новинками и т.д.
     *
     * Поддерживаемые типы блоков: personalplaylists, promotions, new-releases, new-playlists,
     * mixes, chart, artists, albums, playlists, play_contexts.
     *
     * @param array|string $blocks
     *
     * @return mixed parsed json
     */
    public function landing($blocks) {
        $url = $this->baseUrl."/landing3?blocks=";

        if (is_array($blocks)) {
            $url .= implode(',', $blocks);
        }else{
            $url .= $blocks;
        }

        $response = json_decode($this->get($url));
        if($response->result == null) {
            $response = $response->error;
        }else{
            $response = $response->result;
        }

        return $response;
    }

    /**
     * Получение жанров музыки
     *
     * @return mixed parsed json
     */
    public function genres() {
        $url = $this->baseUrl."/genres";

        $result = json_decode($this->get($url))->result;

        return $result;
    }

    /**
     * Получение информации о доступных вариантах загрузки трека
     *
     * @param string|int $trackId Уникальный идентификатор трека
     * @param bool $getDirectLinks Получить ли при вызове метода прямую ссылку на загрузку
     *
     * @return mixed parsed json
     */
    public function tracksDownloadInfo($trackId, $getDirectLinks = false) {
        $result = array();
        $url = $this->baseUrl."/tracks/$trackId/download-info";

        $response = json_decode($this->get($url));
        if($response->result == null) {
            $result = $response->error;
        }else{
            if ($getDirectLinks) {
                foreach ($response->result as $item) {
                    /**
                     * Кодек AAC убран умышлено, по причине генерации
                     * инвалидных прямых ссылок на скачивание
                     */
                    if ($item->codec == 'mp3') {
                        $item->directLink = $this->getDirectLink($item->downloadInfoUrl, $item->codec, $item->bitrateInKbps);
                        unset($item->downloadInfoUrl);
                        array_push($result, $item);
                    }
                }
            }else{
                $result = $response->result;
            }
        }

        return $result;
    }

    /**
     * Получение прямой ссылки на загрузку из XML ответа
     *
     * Метод доступен только одну минуту с момента
     * получения информациио загрузке, иначе 410 ошибка!
     *
     * TODO: перенести загрузку файла в другую функию
     *
     * @param string $url xml-файл с информацией
     * @param string $codec Кодек файла
     *
     * @return string Прямая ссылка на загрузку трека
     */
    public function getDirectLink($url, $codec = 'mp3', $suffix = "1") {
        $response = $this->requestYandexAPI->getXml($url);

        $md5 = md5('XGRlBW9FXlekgbPrRHuSiA'.substr($response->path, 1).$response->s);
        $urlBody = "/get-$codec/$md5/".$response->ts.$response->path;
        $link = "https://".$response->host.$urlBody;
        //$link = "https://".$response->host."/get-".$codec."/randomTrash/".$response->ts.$response->path;
        //$this->requestYandexAPI->download($link, $codec, $suffix);

        return $link;
    }

    /**
     * Метод для отправки текущего состояния прослушиваемого трека
     *
     * TODO: метод не был протестирован!
     *
     * @param string|int $trackId Уникальный идентификатор трека
     * @param string $from Наименования клиента
     * @param string|int $albumId Уникальный идентификатор альбома
     * @param int $playlistId Уникальный идентификатор плейлиста, если таковой прослушивается.
     * @param bool $fromCache Проигрывается ли трек с кеша
     * @param string $playId Уникальный идентификатор проигрывания
     * @param int $trackLengthSeconds Продолжительность трека в секундах
     * @param int $totalPlayedSeconds Сколько было всего воспроизведено трека в секундах
     * @param int $endPositionSeconds Окончательное значение воспроизведенных секунд
     * @param string $client_now Текущая дата и время клиента в ISO
     *
     * @return boolean
     *
     * @throws Exception
     */
    private function playAudio($trackId,
                               $from,
                               $albumId,
                               $playlistId = null,
                               $fromCache = false,
                               $playId = null,
                               $trackLengthSeconds = 0,
                               $totalPlayedSeconds = 0,
                               $endPositionSeconds = 0,
                               $client_now = null
    ) {
        $url = $this->baseUrl."/play-audio";

        $data = array(
            'track-id' => $trackId,
            'from-cache' => $fromCache,
            'from' => $from,
            'play-id' => $playId,
            'uid' => $this->account->uid,
            'timestamp' => (new \DateTime())->format(DateTime::ATOM),
            'track-length-seconds' => $trackLengthSeconds,
            'total-played-seconds' => $totalPlayedSeconds,
            'end-position-seconds' => $endPositionSeconds,
            'album-id' => $albumId,
            'playlist-id' => $playlistId,
            'client-now' => (new \DateTime())->format(DateTime::ATOM)
        );

        $response = $this->post($url, $data);

        return $response;
    }

    /**
     * Получение альбома по его уникальному идентификатору вместе с треками
     *
     * @param string|int $albumId Уникальный идентификатор альбома
     *
     * @return mixed parsed json
     */
    public function albumsWithTracks($albumId) {
        $url = $this->baseUrl."/albums/$albumId/with-tracks";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * Осуществление поиска по запросу и типу, получение результатов
     *
     * @param string $text Текст запроса
     * @param bool $noCorrect Без исправлений?
     * @param string $type Среди какого типа искать (трек, плейлист, альбом, исполнитель)
     * @param int $page Номер страницы
     * @param bool $playlistInBest Выдавать ли плейлисты лучшим вариантом поиска
     *
     * @return mixed parsed json
     */
    public function search($text,
                           $noCorrect = false,
                           $type = 'all',
                           $page = 0,
                           $playlistInBest = true
    ) {
        $url = $this->baseUrl."/search"
            ."?text=$text"
            ."&nocorrect=$noCorrect"
            ."&type=$type"
            ."&page=$page"
            ."&playlist-in-best=$playlistInBest";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * Получение подсказок по введенной части поискового запроса.
     *
     * @param string $part Часть поискового запроса
     *
     * @return mixed parsed json
     */
    public function searchSuggest($part) {
        $url = $this->baseUrl."/search/suggest?part=$part";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * Получение плейлиста или списка плейлистов по уникальным идентификаторам
     *
     * TODO: метод не был протестирован!
     *
     * @param string|int|array $kind Уникальный идентификатор плейлиста
     * @param int $userId Уникальный идентификатор пользователя владеющего плейлистом
     *
     * @return mixed parsed json
     */
    public function usersPlaylists($kind, $userId = null) {
        if ($userId == null) {
            $userId = $this->account->uid;
        }

        $url = $this->baseUrl."/users/$userId/playlists";

        $data = array(
            'kind' => $kind
        );

        $response = json_decode($this->post($url, $data));

        return $response;
    }

    /**
     * Создание плейлиста
     *
     * @param string $title Название
     * @param string $visibility Модификатор доступа
     *
     * @return mixed parsed json
     */
    public function usersPlaylistsCreate($title, $visibility = 'public') {
        $url = $this->baseUrl."/users/".$this->account->uid."/playlists/create";

        $data = array(
            'title' => $title,
            'visibility' => $visibility
        );

        $response = json_decode($this->post($url, $data))->result;

        return $response;
    }

    /**
     * Удаление плейлиста
     *
     * @param string|int $kind Уникальный идентификатор плейлиста
     *
     * @return mixed decoded json
     */
    public function usersPlaylistsDelete($kind) {
        $url = $this->baseUrl."/users/".$this->account->uid."/playlists/$kind/delete";

        $result = json_decode($this->post($url))->result;

        return $result;
    }

    /**
     * Изменение названия плейлиста
     *
     * @param string|int $kind Уникальный идентификатор плейлиста
     * @param string $name Новое название
     *
     * @return mixed decoded json
     */
    public function usersPlaylistsNameChange($kind, $name) {
        $url = $this->baseUrl."/users/".$this->account->uid."/playlists/$kind/name";

        $data = array(
            'value' => $name
        );

        $result = json_decode($this->post($url, $data))->result;

        return $result;
    }

    /**
     * Изменение плейлиста.
     *
     * TODO: функция не готова, необходим воспомогательный класс для получения отличий
     *
     * @param string|int $kind Уникальный идентификатор плейлиста
     * @param string $diff JSON представления отличий старого и нового плейлиста
     * @param int $revision TODO
     *
     * @return mixed parsed json
     */
    private function usersPlaylistsChange($kind, $diff, $revision = 1) {
        $url = $this->baseUrl."/users/".$this->account->uid."/playlists/$kind/change";

        $data = array(
            'kind' => $kind,
            'revision' => $revision,
            'diff' => $diff
        );

        $response = json_decode($this->post($url, $data))->result;

        return $response;
    }

    /**
     * Добавление трека в плейлист
     *
     * TODO: функция не готова, необходим воспомогательный класс для получения отличий
     *
     * @param string|int $kind Уникальный идентификатор плейлиста
     * @param string|int $trackId Уникальный идентификатор трека
     * @param string|int $albumId Уникальный идентификатор альбома
     * @param int $at Индекс для вставки
     * @param int $revision TODO
     *
     * @return mixed parsed json
     */
    public function usersPlaylistsInsertTrack($kind, $trackId, $albumId, $at = 0, $revision = 1) {
        return 'disable';
    }

    /* ROTOR FUNC HERE */

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @return mixed parsed json
     */
    public function rotorAccountStatus() {
        $url = $this->baseUrl."/rotor/account/status";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @return mixed parsed json
     */
    public function rotorStationsDashboard() {
        $url = $this->baseUrl."/rotor/stations/dashboard";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @param string $lang Язык ответа API в ISO 639-1
     *
     * @return mixed parsed json
     */
    public function rotorStationsList($lang = 'en') {
        $url = $this->baseUrl."/rotor/stations/list?language=".$lang;

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @param string $genre Жанр
     * @param string $type
     * @param string $from
     * @param string|int $batchId
     * @param string $trackId
     *
     * @return mixed parsed json
     *
     * @throws Exception
     */
    public function rotorStationGenreFeedback($genre, $type, $from = null, $batchId = null, $trackId = null) {
        $url = $this->baseUrl."/rotor/station/genre:$genre/feedback";
        if ($batchId != null) {
            $url .= "?batch-id=".$batchId;
        }

        $data = array(
            'type' => $type,
            'timestamp' => (new \DateTime())->format(DateTime::ATOM)
        );
        if ($from != null) {
            $data['from'] = $from;
        }
        if ($trackId != null) {
            $data['trackId'] = $trackId;
        }

        $response = json_decode($this->post($url, $data))->result;

        return $response;
    }

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @param string $genre
     * @param string $from
     *
     * @return mixed parsed json
     *
     * @throws Exception
     */
    public function rotorStationGenreFeedbackRadioStarted($genre, $from) {
        return $this->rotorStationGenreFeedback($genre, 'radioStarted', $from);
    }

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @param string $genre
     * @param string $from
     *
     * @return mixed parsed json
     *
     * @throws Exception
     */
    public function rotorStationGenreFeedbackTrackStarted($genre, $from) {
        return $this->rotorStationGenreFeedback($genre, 'trackStarted', $from);
    }

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @param string $genre
     *
     * @return mixed parsed json
     */
    public function rotorStationGenreInfo($genre) {
        $url = $this->baseUrl."/rotor/station/genre:$genre/info";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @param string $genre
     *
     * @return mixed parsed json
     */
    public function rotorStationGenreTracks($genre) {
        $url = $this->baseUrl."/rotor/station/genre:$genre/tracks";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /* ROTOR FUNC END */

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @param string|int $artistId
     *
     * @return mixed parsed json
     */
    public function artistsBriefInfo($artistId) {
        $url = $this->baseUrl."/artists/$artistId/brief-info";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @param string $objectType
     * @param string|int|array $ids
     * @param bool $remove
     *
     * @return mixed parsed json
     */
    private function likeAction($objectType, $ids, $remove = false) {
        $action = 'add-multiple';
        if ($remove) {
            $action = 'remove';
        }
        $url = $this->baseUrl."/users/".$this->account->uid."/likes/".$objectType."s/$action";

        $data = array(
            $objectType.'-ids' => $ids
        );

        $response = json_decode($this->post($url, $data))->result;

        if ($objectType == 'track') {
            $response = $response->revision;
        }

        return $response;
    }

    public function usersLikesTracksAdd($trackIds) {
        return $this->likeAction('track', $trackIds);
    }

    public function usersLikesTracksRemove($trackIds) {
        return $this->likeAction('track', $trackIds, true);
    }

    public function usersLikesArtistsAdd($artistIds) {
        return $this->likeAction('artist', $artistIds);
    }

    public function usersLikesArtistsRemove($artistIds) {
        return $this->likeAction('artist', $artistIds, true);
    }

    public function usersLikesPlaylistsAdd($playlistIds) {
        return $this->likeAction('playlist', $playlistIds);
    }

    public function usersLikesPlaylistsRemove($playlistIds) {
        return $this->likeAction('playlist', $playlistIds, true);
    }

    public function usersLikesAlbumsAdd($albumIds) {
        return $this->likeAction('album', $albumIds);
    }

    public function usersLikesAlbumsRemove($albumIds) {
        return $this->likeAction('album', $albumIds, true);
    }

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @param string $objectType
     * @param string|int|array $ids
     *
     * @return mixed parsed json
     */
    private function getList($objectType, $ids) {
        $url = $this->baseUrl."/".$objectType."s";
        if ($objectType == 'playlist') {
            $url .= "/list";
        }

        $data = array(
            $objectType.'-ids' => $ids
        );

        $response = json_decode($this->post($url, $data))->result;

        return $response;
    }

    public function artists($artistIds) {
        return $this->getList('artist', $artistIds);
    }

    public function albums($albumIds) {
        return $this->getList('album', $albumIds);
    }

    public function tracks($trackIds) {
        return $this->getList('track', $trackIds);
    }

    public function playlistsList($playlistIds) {
        return $this->getList('playlist', $playlistIds);
    }

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @return mixed parsed json
     */
    public function usersPlaylistsList() {
        $url = $this->baseUrl."/users/".$this->account->uid."/playlists/list";

        $response = json_decode($this->get($url))->result;

        return $response;
    }

    /**
     * Получения списка лайков
     *
     * @param string $objectType track, album, artist, playlist
     *
     * @return mixed decoded json
     */
    private function getLikes($objectType) {
        $url = $this->baseUrl."/users/".$this->account->uid."/likes/".$objectType."s";

        $response = json_decode($this->get($url))->result;

        if ($objectType == "track") {
            return $response->library;
        }

        return $response;
    }

    public function getLikesTracks() {
        return $this->getLikes('track');
    }

    public function getLikesAlbums() {
        return $this->getLikes('album');
    }

    public function getLikesArtists() {
        return $this->getLikes('artist');
    }

    public function getLikesPlaylists() {
        return $this->getLikes('playlist');
    }

    /**
     * TODO: Описание функции
     *
     * @param int $ifModifiedSinceRevision
     *
     * @return mixed parsed json
     */
    public function usersDislikesTracks($ifModifiedSinceRevision = 0) {
        $url = $this->baseUrl."/users/".$this->account->uid."/dislikes/tracks"
            .'?if_modified_since_revision='.$ifModifiedSinceRevision;

        $response = json_decode($this->get($url))->result->library;

        return $response;
    }

    /**
     * TODO: Описание функции
     *
     * TODO: метод не был протестирован!
     *
     * @param string|int|array $ids
     * @param bool $remove
     *
     * @return mixed parsed json
     */
    private function dislikeAction($ids, $remove = false) {
        $action = 'add-multiple';
        if ($remove) {
            $action = 'remove';
        }
        $url = $this->baseUrl."/users/".$this->account->uid."/dislikes/tracks/$action";

        $data = array(
            'track-ids-ids' => $ids
        );

        $response = json_decode($this->post($url, $data))->result;

        return $response;
    }

    public function users_dislikes_tracks_add($trackIds) {
        return $this->dislikeAction($trackIds);
    }

    public function users_dislikes_tracks_remove($trackIds) {
        return $this->dislikeAction($trackIds, true);
    }

    private function post($url, $data = null) {
        return $this->requestYandexAPI->post($url, $data);
    }

    private function get($url) {
        return $this->requestYandexAPI->get($url);
    }
}

?>