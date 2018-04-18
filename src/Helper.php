<?php

namespace iMemento\SDK\Auth;

use Auth;
use Crypt;
use Session;
use Redirect;
use Request;
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
     * @param string $callback_url
     * @param string $return_url
     * @return mixed
     */
    public static function redirect(string $callback_url, string $return_url)
    {
        $callback_url = urlencode($callback_url);
        $return_url = urlencode($return_url);

        $scheme = Request::secure() ? 'https://' : 'http://';

        $url = $scheme . env(self::$url_key) . "/login?app_type=fsa&callback_url=$callback_url&return_url=$return_url";
        return redirect()->away($url);
    }

    /**
     * @param string $auth_jwt
     * @param        $auth_public_key
     * @param object $user
     * @param array  $permissions
     * @return object
     */
    public static function handleCallback(string $auth_jwt, $auth_public_key, object $user, array $permissions)
    {
        $app_name = env(self::$app_name);

        $decrypted = JWT::decode($auth_jwt, $auth_public_key);

        $user->id = $decrypted->user_id;
        $user->org_ids = $decrypted->org_ids;
        $user->org_user_ids = $decrypted->org_user_ids;
        $user->roles = $decrypted->roles->$app_name;

        $user->createPermissions($permissions, $user->roles);

        Session::put('auth_jwt', $auth_jwt);
        Session::put('user', Crypt::encryptString($user->toJson())); //used in the static user provider

        Auth::login($user);
    }

}