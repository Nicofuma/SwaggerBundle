<?php

namespace Nicofuma\SwaggerBundle\Tests\Behat\Context;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use FR3D\SwaggerAssertions\PhpUnit\SymfonyAssertsTrait;
use Nicofuma\SwaggerBundle\Validator\ValidatorMap;
use Sanpi\Behatch\Context\BaseContext;

class SwaggerContext extends BaseContext
{
    use SymfonyAssertsTrait;

    /** @var ValidatorMap */
    private $map;

    public function __construct(ValidatorMap $map)
    {
        $this->map = $map;
    }

    /**
     * This method match the response against its Swagger definition.
     *
     * @throws \Behat\Mink\Exception\ExpectationException
     * @throws \Exception
     *
     * @Then the response should match the Swagger definition
     */
    public function theResponseMatchSwagger()
    {
        $response = $this->getResponse();
        $request = $this->getRequest();
        $schemaManager = $this->map->getValidator($request)->getSchemaManager();

        $this->assertResponseMatch($response, $schemaManager, $request->getPathInfo(), $request->getMethod());
    }

    /**
     * @return \Symfony\Component\BrowserKit\Client
     *
     * @throws UnsupportedDriverActionException
     */
    protected function getClient()
    {
        $driver = $this->getSession()->getDriver();

        if (!$driver instanceof BrowserKitDriver) {
            throw new UnsupportedDriverActionException('This step is only supported by the BrowserKitDriver, not the %s one', $driver);
        }

        return $driver->getClient();
    }

    /**
     * Get the last response.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResponse()
    {
        return $this->getClient()->getResponse();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->getClient()->getRequest();
    }
}
