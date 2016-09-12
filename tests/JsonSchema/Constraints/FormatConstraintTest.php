<?php

namespace tests\Nicofuma\SwaggerBundle\JsonSchema\Constraints;

use Nicofuma\SwaggerBundle\Exception\FormatConstraintException;
use Nicofuma\SwaggerBundle\JsonSchema\Constraints\Format\FormatValidatorInterface;
use Nicofuma\SwaggerBundle\JsonSchema\Constraints\FormatConstraint;

/**
 * @covers \Nicofuma\SwaggerBundle\JsonSchema\Constraints\FormatConstraint
 */
class FormatConstraintTest extends \PHPUnit_Framework_TestCase
{
    public function testInvalidSchema()
    {
        $constraint = new FormatConstraint();
        $constraint->check('foo', null);

        static::assertEmpty($constraint->getErrors());
    }

    public function testCheckNotSupportedFormat()
    {
        $format = (object) ['format' => 'foo_bar'];
        $constraint = new FormatConstraint();
        $constraint->check('foo', $format);

        static::assertEmpty($constraint->getErrors());
    }

    public function testCheckExistingFormat()
    {
        $format = (object) ['format' => 'color'];
        $constraint = new FormatConstraint();
        $constraint->check('foo', $format);

        static::assertCount(1, $constraint->getErrors());
        static::assertSame('Invalid color', $constraint->getErrors()[0]['message']);
    }

    public function testCheckNewFormat()
    {
        $formatValidator = $this->prophesize(FormatValidatorInterface::class);
        $formatValidator->validate('foo')->will(function() {
            throw new FormatConstraintException(['Invalid foo']);
        });

        $format = (object) ['format' => 'foo_bar'];
        $constraint = new FormatConstraint();
        $constraint->addFormatValidator('foo_bar', $formatValidator->reveal());
        $constraint->check('foo', $format);

        static::assertCount(1, $constraint->getErrors());
        static::assertSame('Invalid foo', $constraint->getErrors()[0]['message']);
    }
}
