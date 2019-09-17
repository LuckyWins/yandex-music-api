<?php

include 'src/client.php';
//include 'src/utils/utils.php';

header('Content-Type: application/json');
//request
//https://music.yandex.ru/api/v2.1/handlers/track/56550763:8384475/web-own_playlists-playlist-track-main/download/m?hq=1&external-domain=music.yandex.ru&overembed=no&__t=1568366516285

//response
$jsonString = '{"codec":"mp3","bitrate":320,"src":"https://storage.mds.yandex.net/file-download-info/1682435_b856ae96.84573334.7.56550763/320?sign=fbdfc4734985bb3cb57b625770fa6e1aa889204160ed9bd98e752caecc172451&ts=5d7b5ff0","gain":false,"preview":false}';
$jsonData = json_decode($jsonString);
echo "from download-info\"\n\n";
Utils::jsonEncode($jsonData);


//reuqest
//https://storage.mds.yandex.net/file-download-info/1682435_b856ae96.84573334.7.56550763/320?sign=fbdfc4734985bb3cb57b625770fa6e1aa889204160ed9bd98e752caecc172451&ts=5d7b5ff0&format=json&external-domain=music.yandex.ru&overembed=no&__t=1568366516395

//response
$jsonString1 = '{"s":"b5bd1d02226ce24525483fcb82d5a96c9ab3cef452d311027459d6f5068acbc6","ts":"0005926c9fb2b88d","path":"/rmusic/U2FsdGVkX19Rrk1U2n2MQFyvZUSusxX2ynzX1SDKeODxVlv3Bwa-jcOugVWH98f0heyyMv272sihiwqpH_SMT31prHzc8J_xqElNCq83iYY/b5bd1d02226ce24525483fcb82d5a96c9ab3cef452d311027459d6f5068acbc6","host":"s154iva.storage.yandex.net"}';
$jsonData1 = json_decode($jsonString1);
echo "\n\nfrom downloadInfoUrl\"\n\n";
Utils::jsonEncode($jsonData1);

//request
echo "\n\nmake URL\n\n";
$md5 = md5('XGRlBW9FXlekgbPrRHuSiA'.substr($jsonData1->path, 1).$jsonData1->s);
$urlBody = "/get-".$jsonData->codec."/".$md5."/".$jsonData1->ts.$jsonData1->path;
$url = "https://".$jsonData1->host.$urlBody;
echo $url;
$validUrl = 'https://s154iva.storage.yandex.net/get-mp3/25018e5b23ddb01272cb8e50fd69a106/0005926c9fb2b88d/rmusic/U2FsdGVkX19Rrk1U2n2MQFyvZUSusxX2ynzX1SDKeODxVlv3Bwa-jcOugVWH98f0heyyMv272sihiwqpH_SMT31prHzc8J_xqElNCq83iYY/b5bd1d02226ce24525483fcb82d5a96c9ab3cef452d311027459d6f5068acbc6?track-id=56550763&play=false';
echo "\n".$validUrl;

//response
//mp3

?>