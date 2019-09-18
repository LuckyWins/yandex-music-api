<?php

include 'utils.php';

class RequestYandexAPI {

    private $headers = array(
        'X-Yandex-Music-Client: WindowsPhone/3.17',
        'User-Agent: Windows 10',
        'Connection: Keep-Alive'
    );

    private $user;
    private $token;

    /**
     * RequestYandexAPI constructor.
     * @param string $token Уникальный ключ для аутентификации
     * @param string $user Имя пользователя (для логирования)
     */
    public function __construct($token = "", $user = "") {
        if ($token != "") {
            $this->token = $token;
            array_push($this->headers, 'Authorization: OAuth '.$token);
        }
        if ($user != "") {
            $this->user = $user;
        }
    }

    public function updateToken($token) {
        $this->token = $token;
        array_push($this->headers, 'Authorization: OAuth '.$token);
    }

    public function updateUser($user) {
        $this->user = $user;
    }

    public function post($url, $data) {
        $msg = $url;
        if($this->user != "") {
            $msg .= " User: ".$this->user;
        }
        Logger::message($msg, "request.php", "POST");

        $query = http_build_query($data);

        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => $this->headers,
                'content' => $query
            )
        );
        $context = stream_context_create($opts);

        return file_get_contents($url, false, $context);
    }

    public function get($url) {
        $msg = $url;
        if($this->user != "") {
            $msg .= " User: ".$this->user;
        }
        Logger::message($msg, "request.php", "GET");

        $opts = array('http' =>
            array(
                'method' => 'GET',
                'header' => $this->headers,
            )
        );
        $context = stream_context_create($opts);

        return file_get_contents($url, false, $context);
    }

    public function getXml($url) {
        $msg = $url;
        if($this->user != "") {
            $msg .= " User: ".$this->user;
        }
        Logger::message($msg, "request.php", "GET_XML");

        return simplexml_load_file($url);
    }

    /**
     * Загрузка трека по direct url
     *
     * TODO: адекватное название сохраняемого файла
     *
     * @param string $url Ссылка на файл
     * @param string $name Название сохраняемого файла
     * @return bool|int
     */
    public function download($url, $name) {
        $msg = $url;
        if($this->user != "") {
            $msg .= " User: ".$this->user;
        }
        Logger::message($msg, "request.php", "DOWNLOAD");

        return file_put_contents(dirname(__FILE__) . '/'.$name.'.mp3', fopen($url, 'r'));
    }

}