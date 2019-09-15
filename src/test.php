<?php

include 'src/utils/utils.php';
include 'src/yandex.php';

//header('Content-Type: application/json');
//Logger::message("message", __FILE__, 'Info');
//Yandex::getTrackLinks('1673579_077d9c4e.10773081.1.8316120');
//$play = Yandex::getTrackLinks('1673579_077d9c4e.10773081.1.8316120');
//echo json_encode($play);


//request
//https://music.yandex.ru/api/v2.1/handlers/track/56550763:8384475/web-own_playlists-playlist-track-main/download/m?hq=1&external-domain=music.yandex.ru&overembed=no&__t=1568366516285

//response
//{"codec":"mp3","bitrate":320,"src":"https://storage.mds.yandex.net/file-download-info/1682435_b856ae96.84573334.7.56550763/320?sign=fbdfc4734985bb3cb57b625770fa6e1aa889204160ed9bd98e752caecc172451&ts=5d7b5ff0","gain":false,"preview":false}


//reuqest
//https://storage.mds.yandex.net/file-download-info/1682435_b856ae96.84573334.7.56550763/320?sign=fbdfc4734985bb3cb57b625770fa6e1aa889204160ed9bd98e752caecc172451&ts=5d7b5ff0&format=json&external-domain=music.yandex.ru&overembed=no&__t=1568366516395


//response
//{"s":"b5bd1d02226ce24525483fcb82d5a96c9ab3cef452d311027459d6f5068acbc6","ts":"0005926c9fb2b88d","path":"/rmusic/U2FsdGVkX19Rrk1U2n2MQFyvZUSusxX2ynzX1SDKeODxVlv3Bwa-jcOugVWH98f0heyyMv272sihiwqpH_SMT31prHzc8J_xqElNCq83iYY/b5bd1d02226ce24525483fcb82d5a96c9ab3cef452d311027459d6f5068acbc6","host":"s154iva.storage.yandex.net"}


//request
//https://s154iva.storage.yandex.net/get-mp3/25018e5b23ddb01272cb8e50fd69a106/0005926c9fb2b88d/rmusic/U2FsdGVkX19Rrk1U2n2MQFyvZUSusxX2ynzX1SDKeODxVlv3Bwa-jcOugVWH98f0heyyMv272sihiwqpH_SMT31prHzc8J_xqElNCq83iYY/b5bd1d02226ce24525483fcb82d5a96c9ab3cef452d311027459d6f5068acbc6?track-id=56550763&play=false

//response
//mp3
?>