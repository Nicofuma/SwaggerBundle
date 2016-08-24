<?php

namespace tests\SwaggerValidationBundle\Validator;

use FR3D\SwaggerAssertions\SchemaManager;
use SwaggerValidationBundle\Validator\Validator;
use Tests\SwaggerValidationBundle\SwaggerTestCase;

/**
 * @covers \SwaggerValidationBundle\Validator\Validator
 */
class ValidatorTest extends SwaggerTestCase
{
    /**
     * @var SchemaManager
     */
    protected $schemaManager;

    protected function setUp()
    {
        $this->schemaManager = SchemaManager::fromUri('file://'.__DIR__.'/../fixtures/swagger.json');
    }

    public function testValidateMissingSchemaStrict()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Request URI does not match with any swagger path definition');

        $request = $this->createMockRequest('GET', '/api/v1/missing', $this->getValidHeaders(), '', []);

        $validator = new Validator($this->schemaManager, true);
        $validator->validate($request);
    }

    public function testValidateMissingSchema()
    {
        $request = $this->createMockRequest('GET', '/api/v1/missing', $this->getValidHeaders(), '', []);

        $validator = new Validator($this->schemaManager, false);
        $validator->validate($request);

        static::assertTrue(true);
    }

    public function testValidateValid()
    {
        $request = $this->createMockRequest('GET', '/api/v1/users', $this->getValidHeaders(), '', []);

        $validator = new Validator($this->schemaManager, true);
        $validator->validate($request);

        static::assertTrue(true);
    }

    public function testValidateInvalid()
    {
        $this->expectException(\PHPUnit_Framework_ExpectationFailedException::class);
        $this->expectExceptionMessageRegExp('#Failed asserting that \{"count":"bar"\} is a valid request query\..*#');

        $request = $this->createMockRequest('GET', '/api/v1/users?count=bar', $this->getValidHeaders(), '', []);

        $validator = new Validator($this->schemaManager, true);
        $validator->validate($request);
    }
}
