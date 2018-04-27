<?php

namespace iMemento\SDK\Auth;

use GuzzleHttp\Client;
use Request;

/**
 * Class Client
 *
 * @package iMemento\SDK\Auth
 */
class AuthService
{
    /** @var string  */
    protected $url_key = 'ENDPOINT_SSR_AUTH';

    /**
     * @var Client
     */
    protected $client;

    /**
     * AuthService constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Attempts to get auth_jwt for credentials
     *
     * @param string $email
     * @param string $password
     * @return mixed
     */
    public function authenticate(string $email, string $password)
    {
        $scheme = Request::secure() ? 'https://' : 'http://';
        $url = $scheme . env($this->url_key) . 'api/v1/authenticate';

        $data = [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                'email' => $email ?? env('APP_EMAIL'),
                'password' => $password ?? env('APP_PASSWORD'),
            ],
        ];

        $response = $this->client->post($url, $data);
        return json_decode($response->getBody()->getContents());
    }

}