# linkorb/silex-provider-userbase-client

Provides `UserBase\Client\Client` and `UserBase\Client\UserProvider` from
[userbase/client][] as services named respectively `userbase.client` and
`userbase.user_provider`.


## Install

Install using composer:-

    $ composer require linkorb/silex-provider-userbase-client

Then configure and register the provider:-

    // app/app.php
    use LinkORB\UserBaseClient\Provider\UserBaseClientProvider;
    ...
    $app->register(
        new UserBaseClientProvider,
        ['userbase.client.config' => ['username' => 'my-user',
                                      'password' => 'my-passwd',
                                      'url' => 'http://example.com/api/v1']]
    );


[userbase/client]: <https://github.com/userbase-project/userbase-client-php>
  "userbase/client at GitHub"
