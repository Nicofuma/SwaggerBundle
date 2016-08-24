<?php

namespace tests\SwaggerValidationBundle\Validator;

use SwaggerValidationBundle\Exception\NoValidatorException;
use SwaggerValidationBundle\Validator\ValidatorMap;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Tests\SwaggerValidationBundle\SwaggerTestCase;

/**
 * @covers \SwaggerValidationBundle\Validator\ValidatorMap
 */
class ValidatorMapTest extends SwaggerTestCase
{
    public function testGetValidatorNotFound()
    {
        $this->expectException(NoValidatorException::class);

        $map = [];
        $container = new Container();
        $request = $this->createMockRequest('GET', '/foo/bar');

        $map = new ValidatorMap($container, $map);
        $map->getValidator($request);
    }

    public function testGetValidatorNoMatcher()
    {
        $map = [
            'validator1' => new RequestMatcher('^/$'),
            'validator2' => null,
        ];

        $container = $this->prophesize(Container::class);
        $container->get('validator2')->shouldBeCalled();

        $request = $this->createMockRequest('GET', '/foo/bar');

        $map = new ValidatorMap($container->reveal(), $map);
        $map->getValidator($request);
    }

    public function testGetValidator()
    {
        $map = [
            'validator1' => new RequestMatcher('^/$'),
            'validator2' => new RequestMatcher('^/foo'),
        ];

        $container = $this->prophesize(Container::class);
        $container->get('validator2')->shouldBeCalled();

        $request = $this->createMockRequest('GET', '/foo/bar');

        $map = new ValidatorMap($container->reveal(), $map);
        $map->getValidator($request);
    }
}
