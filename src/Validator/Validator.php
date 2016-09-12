<?php

namespace Nicofuma\SwaggerBundle\Validator;

use FR3D\SwaggerAssertions\PhpUnit\SymfonyAssertsTrait;
use FR3D\SwaggerAssertions\SchemaManager;
use JsonSchema\Constraints\Factory;
use Symfony\Component\HttpFoundation\Request;

class Validator
{
    use SymfonyAssertsTrait;

    /** @var SchemaManager */
    private $schemaManager;

    /** @var bool */
    private $strict;

    /** @var Factory */
    private $constraintsFactory;

    public function __construct(Factory $constraintsFactory, SchemaManager $schemaManager, $strict)
    {
        $this->schemaManager = $schemaManager;
        $this->strict = $strict;
        $this->constraintsFactory = $constraintsFactory;
    }

    public function validate(Request $request)
    {
        try {
            $this->assertRequestMatch($request, $this->schemaManager);
        } catch (\RuntimeException $e) {
            if ($this->strict || $e->getMessage() !== 'Request URI does not match with any swagger path definition') {
                throw $e;
            }
        }
    }

    /**
     * @return SchemaManager
     */
    public function getSchemaManager()
    {
        return $this->schemaManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidator()
    {
        return new \JsonSchema\Validator($this->constraintsFactory);
    }
}
