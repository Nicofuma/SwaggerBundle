<?php

namespace tests\SwaggerValidationBundle\EventListener;

use SwaggerValidationBundle\EventListener\ValidatorListener;
use SwaggerValidationBundle\Exception\ConstraintViolationException;
use SwaggerValidationBundle\Exception\NoValidatorException;
use SwaggerValidationBundle\Validator\Validator;
use SwaggerValidationBundle\Validator\ValidatorMap;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\SwaggerValidationBundle\SwaggerTestCase;

/**
 * @covers \SwaggerValidationBundle\EventListener\ValidatorListener
 */
class ValidatorListenerTest extends SwaggerTestCase
{
    public function testOnKernelRequestNoValidator()
    {
        $kernel = $this->prophesize(KernelInterface::class);

        $request = $this->createMockRequest('GET', '/none', []);

        $map = $this->prophesize(ValidatorMap::class);
        $map->getValidator($request)->will(function () {
            throw new NoValidatorException();
        });

        $listener = new ValidatorListener($map->reveal());

        $event = new GetResponseEvent($kernel->reveal(), $request, KernelInterface::MASTER_REQUEST);
        $listener->onKernelRequest($event);

        static::assertTrue(true);
    }

    public function testOnKernelRequest()
    {
        $kernel = $this->prophesize(KernelInterface::class);

        $request = $this->createMockRequest('GET', '/api/v1/users', []);

        $validator = $this->prophesize(Validator::class);
        $validator->validate($request)->shouldBeCalled();

        $map = $this->prophesize(ValidatorMap::class);
        $map->getValidator($request)->willReturn($validator);

        $listener = new ValidatorListener($map->reveal());

        $event = new GetResponseEvent($kernel->reveal(), $request, KernelInterface::MASTER_REQUEST);
        $listener->onKernelRequest($event);

        static::assertTrue(true);
    }

    public function testOnKernelRequestValidationError()
    {
        $this->expectException(ConstraintViolationException::class);

        $kernel = $this->prophesize(KernelInterface::class);

        $request = $this->createMockRequest('GET', '/api/v1/users', []);

        $validator = $this->prophesize(Validator::class);
        $validator->validate($request)->will(function () {
            throw new \PHPUnit_Framework_ExpectationFailedException(
                <<<'EOF'
Failed asserting that {"id":123456789} is a valid request body.
[name] The property name is required
[] Failed to match all schemas

EOF
            );
        })->shouldBeCalled();

        $map = $this->prophesize(ValidatorMap::class);
        $map->getValidator($request)->willReturn($validator);

        $listener = new ValidatorListener($map->reveal());

        $event = new GetResponseEvent($kernel->reveal(), $request, KernelInterface::MASTER_REQUEST);

        try {
            $listener->onKernelRequest($event);
        } catch (ConstraintViolationException $e) {
            $violations = $e->getViolations();

            static::assertCount(2, $violations);
            static::assertSame('name', $violations->get(0)->getPropertyPath());
            static::assertSame('The property name is required', $violations->get(0)->getMessage());
            static::assertSame('', $violations->get(1)->getPropertyPath());
            static::assertSame('Failed to match all schemas', $violations->get(1)->getMessage());

            throw $e;
        }
    }
}
