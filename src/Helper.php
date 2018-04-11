<?php

namespace iMemento\SDK\Auth;

use Auth;
use iMemento\JWT;

/**
 * Class Helper
 *
 * @package iMemento\SDK\Auth
 */
class Helper
{
    /** @var string  */
    protected static $url_key = 'IMEMENTO_SDK_AUTH';

    /** @var string  */
    protected static $app_name = 'APP_NAME';

    /**
     * @param string $return_url
     * @return mixed
     */
    public static function redirect(string $return_url)
    {
        $url = env(self::$url_key) . "/login?app_type=fsa&return_url=$return_url";
        return redirect()->away($url);
    }

    /**
     * @param string $jwt
     * @param        $auth_public_key
     * @param object $user
     * @param array  $permissions
     * @return object
     */
    public static function handleCallback(string $jwt, $auth_public_key, object $user, array $permissions)
    {
        $app_name = env(self::$app_name);
        $decrypted = new \StdClass;

        $user->id = $decrypted->user_id;
        $user->org_ids = $decrypted->org_ids;
        $user->org_user_ids = $decrypted->org_user_ids;
        $user->roles = $decrypted->roles->$app_name;

        $user->createPermissions($permissions, $user->roles);

        Auth::login($user);

        return $user;
    }

}