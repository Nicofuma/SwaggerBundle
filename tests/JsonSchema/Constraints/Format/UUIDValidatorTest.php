<?php

namespace tests\Nicofuma\SwaggerBundle\JsonSchema\Constraints\Format;

use Nicofuma\SwaggerBundle\Exception\FormatConstraintException;
use Nicofuma\SwaggerBundle\JsonSchema\Constraints\Format\UUIDValidator;
use Prophecy\Argument;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @covers \Nicofuma\SwaggerBundle\JsonSchema\Constraints\Format\UUIDValidator
 */
class UUIDValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidUUid()
    {
        $symfonyValidator = $this->prophesize(ValidatorInterface::class);
        $symfonyValidator->validate(Argument::any(), Argument::any())->willReturn(new ConstraintViolationList());

        $validator = new UUIDValidator($symfonyValidator->reveal());
        $validator->validate('12345678-1234-1234-1234-123456789012');

        static::assertTrue(true);
    }

    public function testInValidUUid()
    {
        $this->expectException(FormatConstraintException::class);

        $symfonyValidator = $this->prophesize(ValidatorInterface::class);
        $symfonyValidator->validate(Argument::any(), Argument::any())->willReturn(new ConstraintViolationList([
            new ConstraintViolation('Error', 'Error', [], '', '', ''),
        ]));

        $validator = new UUIDValidator($symfonyValidator->reveal());
        $validator->validate('12345678123412341234123456789012');
    }
}
