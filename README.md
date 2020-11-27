# yandex-music-api
Php lib for Yandex.Music. Big thx @MarshalX

## login example

```php
$token = "";

$client = new Client($token);
$account = $client->getAccount();
if($account == null){
    $client->fromCredentials("username", "paassword" , true);
    // this will print the token
}
```
