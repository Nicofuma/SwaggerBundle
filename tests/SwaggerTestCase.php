<?php

namespace tests\Nicofuma\SwaggerBundle;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

class SwaggerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return string[]
     */
    protected function getValidHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'ETag' => '123',
        ];
    }

    /**
     * @param string   $method
     * @param string   $path
     * @param string[] $headers
     * @param string   $body
     * @param mixed[]  $query
     *
     * @return Request
     */
    protected function createMockRequest($method, $path, array $headers = [], $body = '', $query = [])
    {
        $request = Request::create($path, $method, $query, [], [], [], $body);
        $request->headers = new HeaderBag($headers);

        return $request;
    }
}
