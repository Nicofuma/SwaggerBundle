<?php

namespace SwaggerValidationBundle\Exception;

class NoValidatorException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('No validator match the current request');
    }
}
