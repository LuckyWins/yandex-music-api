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
     * Получение статуса аккаунта
     *
     * @return array
     */
    public function accountStatus() {
        $url = $this->baseUrl."/account/status";

        $result = json_decode($this->get($url))->result;

        return $result;
    }

    /**
     * Обновление статуса аккаунта
     */
    private function updateAccountStatus() {
        $this->account = $this->accountStatus()->account;
    }

    /**
     * Получения списка лайков
     *
     * @param string $objectType track, album, artist, playlist
     * @return mixed
     */
    private function getLikes($objectType) {
        $url = $this->baseUrl."/users/".$this->account->uid."/likes/".$objectType."s";

        $result = json_decode($this->get($url))->result;

        if ($objectType == "track") {
            return $result->library;
        }

        return $result;
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