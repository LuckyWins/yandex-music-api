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
     * Временная функция для отладки
     * УДАЛИТЬ позже
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

    public function tracksDownloadInfo($trackId, $getDirectLinks = false) {
        $result = array();
        $url = $this->baseUrl."/tracks/".$trackId."/download-info";

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
                        $download = $this->getDirectLink($item->downloadInfoUrl);
                        $item->directLink = $download;
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
     * @param string $url xml-файл с информацией
     * @param string $codec Кодек файла
     *
     * @return string Прямая ссылка на загрузку трека
     */
    public function getDirectLink($url, $codec = 'mp3') {
        $response = $this->requestYandexAPI->getXml($url);

        $md5 = md5('XGRlBW9FXlekgbPrRHuSiA'.substr($response->path, 1).$response->s);
        $urlBody = "/get-".$codec."/".$md5."/".$response->ts.$response->path;
        $link = "https://".$response->host.$urlBody;
        //$link = "https://".$response->host."/get-".$codec."/randomTrash/".$response->ts.$response->path;

        //return preg_replace("\\", "", $link);
        return $link;
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

    private function post($url, $data) {
        return $this->requestYandexAPI->post($url, $data);
    }

    private function get($url) {
        return $this->requestYandexAPI->get($url);
    }
}

?>