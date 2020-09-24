<?php

namespace iMemento\SDK\Auth\Tests;

use iMemento\SDK\Auth\User;

class UserTest extends TestCase
{
    public function test_permissions()
    {
        $user = new User();
        $user->createPermissions([
            'admin' => [
                'edit-users',
                'read-articles',
                'delete-articles'
            ],
            'guest' => [
                'read-articles'
            ]
        ], [
            'guest'
        ]);

        $this->assertTrue($user->hasPermission('read-articles'));
        $this->assertFalse($user->hasPermission('delete-articles'));
    }

    public function test_ownership()
    {
        $user = new User(['id' => '751']);

        $owned = (object) [
            'user_id'   => 751,
            'name'      => 'some resource'
        ];

        $not_owned = (object) [
            'user_id'   => 231,
            'name'      => 'some other resource'
        ];

        $this->assertTrue($user->owns($owned));
        $this->assertFalse($user->owns($not_owned));
    }
}
