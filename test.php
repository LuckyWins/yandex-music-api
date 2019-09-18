<?php

include 'src/client.php';
//include 'src/utils/utils.php';

header('Content-Type: application/json');

$jsonString1 = '{"s":"b5bd1d02226ce24525483fcb82d5a96c9ab3cef452d311027459d6f5068acbc6","ts":"0005926c9fb2b88d","path":"/rmusic/U2FsdGVkX19Rrk1U2n2MQFyvZUSusxX2ynzX1SDKeODxVlv3Bwa-jcOugVWH98f0heyyMv272sihiwqpH_SMT31prHzc8J_xqElNCq83iYY/b5bd1d02226ce24525483fcb82d5a96c9ab3cef452d311027459d6f5068acbc6","host":"s154iva.storage.yandex.net"}';
$jsonData1 = json_decode($jsonString1);
$xml = '<?xml version="1.0" encoding="utf-8" ?> 
 <download-info>
  <host>s58myt.storage.yandex.net</host> 
  <path</path> 
  <ts>000592c3e237b1eb</ts> 
  <region>-1</region> 
  <s>ecb2d9a6340dc8f036157957eb1a8dbc1cfd01bb01149da1acf811773e09123d</s> 
';


$jsonData1 = array(
    'host' => 's58myt.storage.yandex.net',
    'path' => '/rmusic/U2FsdGVkX18Qq2fokYyfzN7uEsQWoeJ3HyiitQsS5rqcXjOZC-G5B9XanUSzvATavN5AMLs2oY5Lf2qQEuNS6U6pWwLfVBMe3uifjOVsL9o/ecb2d9a6340dc8f036157957eb1a8dbc1cfd01bb01149da1acf811773e09123d',
    'ts' => '000592c3e237b1eb',
    's' => 'ecb2d9a6340dc8f036157957eb1a8dbc1cfd01bb01149da1acf811773e09123d'
);

//request
echo "\n\nmake URL\n\n";
$md5 = md5('XGRlBW9FXlekgbPrRHuSiA'.substr($jsonData1['path'], 1).$jsonData1['s']);
$urlBody = "/get-mp3/".$md5."/".$jsonData1['ts'].$jsonData1['path'];
$url = "https://".$jsonData1['host'].$urlBody;
echo $url;

//response
//mp3

?>