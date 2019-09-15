<?php

include 'src/client.php';
include 'src/utils/utils.php';

include 'privateConfig.php';
global $privateConfig;

$client = new Client($privateConfig['token']);
//$client->fromCredentials($privateConfig['login'], $privateConfig['password'], true);

//Utils::dump($client->getAccount());

//$result = $client->getLikes('track');
$result = $client->getLikesTracks();
header('Content-Type: application/json');
Utils::jsonEncode($result);
//Utils::dump($result);


?>