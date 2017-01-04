# baidusocialite
openapi.baidu.com OAuth2 Provider for Laravel Socialite

<h6>百度认证（Baidu）<h6>
使用如下Composer命令安装依赖：

    composer require boolw/baidusocialite


注册服务提供者（同时注释掉原有的Socialite提供者）：

```php
//Laravel\Socialite\SocialiteServiceProvider::class,
SocialiteProviders\Manager\ServiceProvider::class,
```

添加Socialite门面（如果已有略过本操作）：

```php
'Socialite' => Laravel\Socialite\Facades\Socialite::class,
```

添加事件监听器（App/Providers/EventServiceProvider）：

```php
protected $listen = [
    'SocialiteProviders\Manager\SocialiteWasCalled' => [
        'SocialiteProviders\Weibo\WeiboExtendSocialite@handle',
    ],
];
```
去新浪微博开放平台（https://openapi.baidu.com/）创建一个新的网站应用以获取相应的App Key和App Secret。需要注意的是不比GitHub，新浪微博需要创建的网站应用对应网站在外网可以访问。

然后在配置文件app/services.php中添加weibo配置项：

```php
'baidu' => [
    'client_id' => 'your weibo app App Key',
    'client_secret' => 'your weibo app App Secret',
    'redirect' => 'http://laravel.app:8000/auth/baidu/callback'
]
```

接下来我们要对AuthController略作修改：

```php
public function redirectToProvider(Request $request,$service)
{
    return Socialite::driver($service)->redirect();
}

public function handleProviderCallback(Request $request,$service)
{
    $user = Socialite::driver($service)->user();
    dd($user);
}
```

以支持多种不同认证提供者的切换。

最后需要修改认证路由规则如下：

```php
Route::get('auth/{service}', 'Auth\AuthController@redirectToProvider');
Route::get('auth/{service}/callback', 'Auth\AuthController@handleProviderCallback');
```

至此就可以在浏览器中访问http://laravel.app:8000/auth/baidu进行测试了。
