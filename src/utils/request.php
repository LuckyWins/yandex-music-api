<?php

class RequestYandexAPI {

    private $headers = array(
        'X-Yandex-Music-Client: WindowsPhone/3.17',
        'User-Agent: Windows 10',
        'Connection: Keep-Alive'
    );

    private $token;

    /**
     * RequestYandexAPI constructor.
     * @param string $token
     */
    public function __construct($token = "") {
        if ($token != "") {
            $this->token = $token;
            array_push($this->headers, 'Authorization: OAuth '.$token);
        }
    }

    public function updateToken($token) {
        $this->token = $token;
        array_push($this->headers, 'Authorization: OAuth '.$token);
    }

    public function post($url, $data) {
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
        $opts = array('http' =>
            array(
                'method' => 'GET',
                'header' => $this->headers,
            )
        );
        $context = stream_context_create($opts);

        return file_get_contents($url, false, $context);
    }

}