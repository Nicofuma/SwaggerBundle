<?php

namespace Nicofuma\SwaggerBundle\Tests\Behat\Context;

use BootstrapBundle\Tests\Behat\Context\Traits\ClientContextTrait;
use FR3D\SwaggerAssertions\PhpUnit\SymfonyAssertsTrait;
use Sanpi\Behatch\Context\BaseContext;
use Nicofuma\SwaggerBundle\Validator\ValidatorMap;

class SwaggerContext extends BaseContext
{
    use ClientContextTrait;
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
}
