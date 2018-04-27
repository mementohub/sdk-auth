<?php

namespace iMemento\SDK\Auth;

use App;
use Auth;
use Crypt;
use Session;
use Redirect;
use Request;
use GuzzleHttp\Client;
use iMemento\JWT\JWT;

/**
 * Class Helper
 *
 * @package iMemento\SDK\Auth
 */
class Helper
{
    /** @var string  */
    protected static $url_key = 'ENDPOINT_SSR_AUTH';

    /** @var string  */
    protected static $app_name = 'APP_NAME';

    /**
     * Redirects to auth login or register
     *
     * @param string $callback_url
     * @param string $return_url
     * @param bool   $register
     * @return mixed
     */
    public static function redirect(string $callback_url, string $return_url, bool $register = false)
    {
        $scheme = Request::secure() ? 'https://' : 'http://';
        $action = $register ? 'register' : 'login';

        $query = http_build_query([
            'app_type' => 'fsa',
            'callback_url' => urlencode($callback_url),
            'return_url' => urlencode($return_url),
            'locale' => App::getLocale() ?? 'en',
        ]);

        $url = $scheme . env(self::$url_key) . "/$action?$query";
        return redirect()->away($url);
    }

    /**
     * Handles the redirect from auth and authenticates the user based on the returned token
     *
     * @param string $auth_jwt
     * @param        $auth_public_key
     * @param        $user
     * @param array  $permissions
     * @param array  $attributes
     */
    public static function handleCallback(string $auth_jwt, $auth_public_key, $user, array $permissions, array $attributes = null)
    {
        $app_name = env(self::$app_name);

        $decrypted = JWT::decode($auth_jwt, $auth_public_key);

        $user->id = $decrypted->user_id;
        $user->org_ids = $decrypted->org_ids;
        $user->org_user_ids = $decrypted->org_user_ids;
        $user->token = $auth_jwt;
        $user->roles = $decrypted->roles->$app_name ?? [];

        //ability to add or overwrite the user's attributes
        if (! empty($attributes)) {
            foreach ($attributes as $k => $v) {
                $user->$k = $v;
            }
        }

        $user->createPermissions($permissions, $user->roles);

        Session::put('user', Crypt::encryptString($user->toJson())); //used in the static user provider

        Auth::login($user);
    }

    /**
     * Authenticates a user (or service) based on email and password, and returns the jwt
     *
     * @param string $email
     * @param string $password
     * @return mixed
     */
    public static function authenticate(string $email, string $password)
    {
        $client = new Client;

        $scheme = Request::secure() ? 'https://' : 'http://';
        $url = $scheme . env(self::$url_key) . 'api/v1/authenticate';

        $data = [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                'email' => $email ?? env('APP_EMAIL'),
                'password' => $password ?? env('APP_PASSWORD'),
            ],
        ];

        $response = $client->post($url, $data);
        return json_decode($response->getBody()->getContents());
    }

}