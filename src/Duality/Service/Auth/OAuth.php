<?php

/**
 * OAuth authentication service
 *
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.20.0
 */

namespace Duality\Service\Auth;

use Duality\Core\DualityException;
use Duality\Service\Auth;
use OAuth as PHPOAuth;

/**
 * Default authentication service
 * 
 * PHP Version 5.3.3
 *
 * @author  Marco Afonso <mafonso333@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    http://github.com/taviroquai/duality
 * @since   0.20.0
 */
class OAuth
extends Auth
{
    /**
     * Holds the database auth configuration
     * 
     * var array The configuration params
     */
    protected $config;

    /**
     * Holds the oauth instance
     * 
     * var \OAuth The oauth instance
     */
    protected $handler;    

    /**
     * Initiates the service
     * 
     * @return void
     */
    public function init()
    {
        if (!$this->app->getConfigItem('auth.url')
            || !$this->app->getConfigItem('auth.key')
            || !$this->app->getConfigItem('auth.secret')
        ) {
            throw new DualityException(
                "Error Config: oauth configuration (url|key|secret) not found", 1
            );
        }
        $this->config = $this->app->getConfigItem('auth');

        // In state=1 the next request should include an oauth_token.
        // If it doesn't go back to 0
        $token = $this->app->call('server')
            ->getRequest()
            ->getParam('oauth_token');
        $status = $this->app->call('session')->get('__oauth_status');
        if (!$token && $status == 1) {
            $this->call('session')->set('__oauth_status', 0);
        }
    }

    /**
     * Terminates the service
     * 
     * @return void
     */
    public function terminate()
    {

    }

    /**
     * Login using a 2-key (username, password)
     * 
     * @param string $username The authentication username (key)
     * @param string $password The authentication password (secret)
     * 
     * @return boolean The authentication result (true or false)
     */
    public function login($username, $password)
    {
        // Make request
        // $username = $this->app->getConfigItem('auth.key');
        // $password = $this->app->getConfigItem('auth.secret');
    
        $this->handler = new PHPOAuth(
            $username, $password, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI
        );

        //$oauth->enableDebug();
        $token = $this->app->call('server')
            ->getRequest()
            ->getParam('oauth_token');
        $status = $this->app->call('session')->get('__oauth_status');
        $secret = $this->app->call('session')->get('__oauth_token');

        // Check status
        if (!$status) {

            // Call endpoint url: header
            // header('Location: '.$url.'?oauth_token='.$req['oauth_token']); exit;
            return false;

        } elseif ($status == 1) {

            // We have token and secret
            $url = $this->config['url'];
            $this->handler->setToken($token, $secret);
            $access = $this->handler->getAccessToken($url);
            $this->app->call('session')->set('__oauth_status', 2);
            $this->app->call('session')->set(
                '__oauth_token', $access['oauth_token']
            );
            $this->app->call('session')->set(
                '__oauth_secret', $access['oauth_token_secret']
            );
        }

        // OK! You can make requests now
        $token = $this->app->call('session')->get('__oauth_token');
        $secret = $this->app->call('session')->get('__oauth_secret');
        $this->handler->setToken($token, $secret);
        // $oauth->fetch("$url/user.json");
        // $json = json_decode($oauth->getLastResponse());

        return true;
    }

    /**
     * Gets the OAuth Acess URL
     * 
     * @return string The access url with token params
     */
    public function getAccessUrl()
    {
        $req = $this->handler->getRequestToken($this->config['url']);
        if (!isset($req['oauth_token_secret'])) {
            return false;
        }

        // OK! We got exchange...
        $this->app->call('session')->set(
            '__oauth_token', $req['oauth_token_secret']
        );
        $this->app->call('session')->set('__oauth_status', 1);
        return $this->config['url'] 
            . '?oauth_token=' 
            . $req['oauth_token'];
    }

    /**
     * Makes a request with valid access token
     * Example: /user.json
     * 
     * @param string $uri The uri to request
     * 
     * @return array The response data
     */
    public function request($uri)
    {
        $url = $this->config['url'] . $uri;
        $this->handler->fetch($url);
        return json_decode($this->handler->getLastResponse());
    }
}