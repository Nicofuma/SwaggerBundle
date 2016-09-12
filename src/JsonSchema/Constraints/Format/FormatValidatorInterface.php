<?php

namespace Nicofuma\SwaggerBundle\JsonSchema\Constraints\Format;

interface FormatValidatorInterface
{
    /**
     * Validates the value of an element
     *
     * @param mixed $value
     *
     * @throws
     */
    public function validate($value);
}
