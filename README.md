# oauth2
oauth2授权,微信(wechat)...

## Install

```shell

composer require weida/oauth2

```


## Demo

```php
use Weida\Oauth2\Weixin;
use Weida\Oauth2Core\Factory;


$config=[
    'client_id' => 'aaaaaa',
    'client_secret' => 'bbbbbbbbbbb',
    'redirect'=>'http://127.0.0.1/a',
];

$app =  Factory::getOauth2(Factory::WEIXIN,$config);
//或者 $app = new Weixin($config);
echo $app->redirect();

```
