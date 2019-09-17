<?php

include 'src/client.php';
//include 'src/utils/utils.php';

include 'privateConfig.php';
global $privateConfig;

header('Content-Type: application/json');

$client = new Client($privateConfig['token']);
//$client->fromCredentials($privateConfig['login'], $privateConfig['password'], true);

//Utils::dump($client->getAccount());

//$result = $client->getLikes('track');
//$result = $client->tracksDownloadInfo("57925627:8751243", true);
$result = $client->usersPlaylistsNameChange(1002, 'new name');
Utils::jsonEncode($result);
//Utils::dump($result);


?>