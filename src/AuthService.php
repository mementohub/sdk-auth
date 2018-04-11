<?php

namespace iMemento\SDK\Auth;

use iMemento\JWT\Issuer;
use iMemento\JWT\JWT;
use iMemento\JWT\Payload;
use GuzzleHttp\Client;

/**
 * Class Client
 *
 * @package iMemento\SDK\Auth
 */
class AuthService
{

    /** @var string  */
    protected $url_key = 'IMEMENTO_SDK_AUTH';

    /**
     * @var Issuer
     */
    protected $issuer;

    /**
     * @var array
     */
    protected $config = [];


    /**
     * Client constructor.
     *
     * @param Issuer $issuer
     * @param array  $config
     */
    public function __construct(Issuer $issuer, array $config = [])
    {
        $this->setDefaultConfig();
        $this->issuer = $issuer;
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Add default values for endpoints and host
     */
    protected function setDefaultConfig()
    {
        $httpHost = imemento_request_scheme() . '://' . env($this->url_key);
        $this->config = [
            'endpoint' => $httpHost . '/login',
            'endpoint_token' => $httpHost . '/api/v1/get-token',
            'host' => $httpHost,
        ];
    }

    /**
     * Redirects to auth
     *
     * @param array $config
     * @return void
     */
    public function attempt(array $config = [])
    {
        //creates a specific payload for the auth service
        $payload = Payload::create([
            'iss' => $this->issuer->name,
            'cbk' => $config['callback_url'],
            'ret' => $config['return_url'],
        ]);

        $jwt = JWT::encode($payload, $this->issuer->private_key);

        header("Location: {$this->config['endpoint']}?jwt=$jwt");
        exit(); //if missing, won't redirect
    }

    /**
     * Parses the callback JWT and returns the user
     *
     * @param $token
     * @param $publicKey
     * @return User
     */
    public function getUser($token, $publicKey)
    {
        $jwt = new JWT($token);
        $payload = $jwt->decode($publicKey);

        $user = Payload::getUser($payload);
        return new User($user);
    }

    /**
     * Refreshes a token, expired or not
     *
     * @return string
     */
    public function getToken()
    {
        $payload = Payload::create([
            'iss' => $this->issuer->name,
            'session_id' => $this->issuer->session_id,
        ]);

        $token = JWT::encode($payload, $this->issuer->private_key);

        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        try {
            $response = $client->request('POST', $this->config['endpoint_token'], ['headers' => ['Host' => $this->config['host']]]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        }

        return json_decode($response->getBody());
    }

}