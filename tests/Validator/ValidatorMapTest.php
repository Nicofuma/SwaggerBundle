<?php

namespace tests\Nicofuma\SwaggerBundle\Validator;

use Nicofuma\SwaggerBundle\Exception\NoValidatorException;
use Nicofuma\SwaggerBundle\Validator\ValidatorMap;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RequestMatcher;
use tests\Nicofuma\SwaggerBundle\SwaggerTestCase;

/**
 * @covers \Nicofuma\SwaggerBundle\Validator\ValidatorMap
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
