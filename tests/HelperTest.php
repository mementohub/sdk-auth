<?php

namespace iMemento\SDK\Auth\Tests;

use iMemento\SDK\Auth\Helper;

class HelperTest extends TestCase
{
    public function test_redirect_url()
    {
        $redirect = Helper::redirect('some_callback_url', 'other_return_url');

        $parsed = parse_url($redirect->headers->get('Location'));

        $this->assertEquals('http', $parsed['scheme']);
        $this->assertEquals('public.test', $parsed['host']);
        $this->assertEquals('/login', $parsed['path']);
    }

    public function test_register_url()
    {
        $redirect = Helper::redirect('some_callback_url', 'other_return_url', [], true);

        $parsed = parse_url($redirect->headers->get('Location'));

        $this->assertEquals('/register', $parsed['path']);
    }

    public function test_redirect_includes_custom_parameters()
    {
        $redirect = Helper::redirect('some_callback_url', 'other_return_url', [
            'foo'   => 'bar',
            'baz'   => 'bat'
        ]);

        $query = parse_url($redirect->headers->get('Location'), PHP_URL_QUERY);
        parse_str($query, $parameters);

        $this->assertEquals('bar', $parameters['foo']);
        $this->assertEquals('bat', $parameters['baz']);
    }

    public function test_redirect_parameters()
    {
        $redirect = Helper::redirect('some_callback_url', 'other_return_url');

        $query = parse_url($redirect->headers->get('Location'), PHP_URL_QUERY);
        parse_str($query, $parameters);

        $this->assertEquals([
            'app_type'     => 'fsa',
            'callback_url' => 'some_callback_url',
            'return_url'   => 'other_return_url',
            'locale'       => 'en'
        ], $parameters);
    }
}
