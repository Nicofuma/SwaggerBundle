<?php

namespace tests\Nicofuma\SwaggerBundle\Swagger;

use FR3D\SwaggerAssertions\SchemaManager;
use Nicofuma\SwaggerBundle\Swagger\SchemaManagerFactory;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @covers \Nicofuma\SwaggerBundle\Swagger\SchemaManagerFactory
 */
class SchemaManagerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SchemaManager
     */
    protected $expectedSchemaManager;

    protected function setUp()
    {
        $this->expectedSchemaManager = SchemaManager::fromUri('file://'.__DIR__.'/../fixtures/swagger.json');
    }

    public function testCreateFromFileExistingFile()
    {
        $kernel = $this->prophesize(KernelInterface::class);
        $factory = new SchemaManagerFactory($kernel->reveal());
        $schemaManager = $factory->createFromFile(__DIR__.'/../fixtures/swagger.json');

        static::assertSame($this->expectedSchemaManager->getPathTemplates(), $schemaManager->getPathTemplates());
    }

    public function testCreateFromFileInAppResources()
    {
        $kernel = $this->prophesize(KernelInterface::class);
        $kernel->getRootDir()->willReturn(__DIR__.'/../fixtures/')->shouldBeCalled();

        $factory = new SchemaManagerFactory($kernel->reveal());
        $schemaManager = $factory->createFromFile('swagger.json');

        static::assertSame($this->expectedSchemaManager->getPathTemplates(), $schemaManager->getPathTemplates());
    }

    public function testCreateFromFileInBundle()
    {
        $kernel = $this->prophesize(KernelInterface::class);
        $kernel->getRootDir()->willReturn(__DIR__.'/../fixtures/')->shouldBeCalled();
        $kernel->locateResource('@MyBundle/swagger.json')->willReturn(__DIR__.'/../fixtures/swagger.json')->shouldBeCalled();

        $factory = new SchemaManagerFactory($kernel->reveal());
        $schemaManager = $factory->createFromFile('@MyBundle/swagger.json');

        static::assertSame($this->expectedSchemaManager->getPathTemplates(), $schemaManager->getPathTemplates());
    }

    public function testCreateFromFileNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);

        $kernel = $this->prophesize(KernelInterface::class);
        $kernel->getRootDir()->willReturn(__DIR__.'/../fixtures/')->shouldBeCalled();
        $kernel->locateResource('@MyBundle/swagger.json')->will(function () {
            throw new \InvalidArgumentException('Unable to find file "%s".');
        })->shouldBeCalled();

        $factory = new SchemaManagerFactory($kernel->reveal());
        $factory->createFromFile('@MyBundle/swagger.json');
    }
}
