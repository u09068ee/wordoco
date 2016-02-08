<?php

/**
 * TwitterOAuth - https://github.com/ricardoper/TwitterOAuth
 * PHP library to communicate with Twitter OAuth API version 1.1
 *
 * @author Ricardo Pereira <github@ricardopereira.es>
 * @copyright 2014
 */

// require __DIR__ . '/../../../../vendor/autoload.php';

use TwitterOAuth\Auth\SingleUserAuth;

/**
 * Serializer Namespace
 */
use TwitterOAuth\Serializer\ArraySerializer;



/**
 * Array with the OAuth tokens provided by Twitter
 *   - consumer_key        Twitter API key
 *   - consumer_secret     Twitter API secret
 */
$credentials = array(
    	'consumer_key' => 'fxy5RXmMPpvMyMlYWVMP0kqx3',
    	'consumer_secret' => 'LT29Fbr5OuVPs40OsyiOpFy7mpdqVUNo0zvEuFC59nk0VXRzJu',
	);

/**
 * Instantiate SingleUser
 *
 * For different output formats you can set one of available serializers
 * (Array, Json, Object, Text or a custom one)
 */
$serializer = new ArraySerializer();

$auth = new SingleUserAuth($credentials, $serializer);


/**
 * Allows a Consumer application to obtain an OAuth Request Token to request user authorization
 *
 * https://dev.twitter.com/oauth/reference/post/oauth/request_token
 */
$params = array(
    'oauth_callback' => '',
);

$response = $auth->post('oauth/request_token', $params);


echo '<pre>'; print_r($auth->getHeaders()); echo '</pre>';

echo '<pre>'; print_r($response); echo '</pre><hr />';
