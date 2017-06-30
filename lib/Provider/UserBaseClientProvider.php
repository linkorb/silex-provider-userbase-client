<?php

namespace LinkORB\UserBaseClient\Provider;

use RuntimeException;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use UserBase\Client\Client;
use UserBase\Client\UserProvider;

/**
 * Provides "userbase.user_provider" and "userbase.client" services
 *
 * Mandatory "userbase.client.config" parameters:-
 *
 *   url: of a Userbase API, e.g. http://example.com/api/v1
 *   username: username credential
 *   password: password credential
 *
 * Optional "userbase.client.config" parameters:-
 *
 *   cache: an instance of \Psr\Cache\CacheItemPoolInterface,
 *          e.g. \Symfony\Component\Cache\Adapter\ApcuAdapter
 *   cache_duration: integer cache entry lifetime in seconds
 *   timer: an instance of \DebugBar\DataCollector\TimeDataCollector
 *
 * Optional "userbase.user_provider.config" parameters:-
 *
 *   refresh_user: boolean true if the provider should refresh Users after
 *                 retrieval from session storage (default: false)
 *
 */
class UserBaseClientProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['userbase.client'] = function ($app) {
            if (!isset($app['userbase.client.config'])) {
                throw new RuntimeException('Missing configuration "userbase.client.config".');
            }
            if (!isset($app['userbase.client.config']['url'])) {
                throw new RuntimeException('Missing "url" configuration from "userbase.client.config".');
            }
            if (!isset($app['userbase.client.config']['username'])) {
                throw new RuntimeException('Missing "username" configuration from "userbase.client.config".');
            }
            if (!isset($app['userbase.client.config']['password'])) {
                throw new RuntimeException('Missing "password" configuration from "userbase.client.config".');
            }

            $client = new Client(
                $app['userbase.client.config']['url'],
                $app['userbase.client.config']['username'],
                $app['userbase.client.config']['password']
            );

            if (isset($app['userbase.client.config']['cache'])
                && isset($app['userbase.client.config']['cache_duration'])
            ) {
                $client->setCache(
                    $app['userbase.client.config']['cache'],
                    $app['userbase.client.config']['cache_duration']
                );
            } elseif (isset($app['userbase.client.config']['cache'])) {
                $client->setCache($app['userbase.client.config']['cache']);
            }
            if (isset($app['userbase.client.config']['timer'])) {
                $client->setTimeDataCollector($app['userbase.client.config']['timer']);
            }

            return $client;
        };

        $app['userbase.user_provider'] = function ($app) {
            $shouldRefresh = false;
            if (isset($app['userbase.user_provider.config'])
                && isset($app['userbase.user_provider.config']['refresh_user'])
            ) {
                $shouldRefresh = (bool) $app['userbase.user_provider.config']['refresh_user'];
            }
            return new UserProvider($app['userbase.client'], $shouldRefresh);
        };
    }
}
