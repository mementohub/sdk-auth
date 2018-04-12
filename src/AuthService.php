<?php

namespace iMemento\SDK\Auth;

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
        $url = env($this->url_key) . 'api/v1/authenticate';

        $data = [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'form_params' => [
                'email' => $email,
                'password' => $password,
            ],
        ];

        $response = $this->client->post($url, $data);

        return json_decode($response->getBody()->getContents());
    }

}