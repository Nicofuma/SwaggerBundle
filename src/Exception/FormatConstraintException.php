<?php

namespace Nicofuma\SwaggerBundle\Exception;

class FormatConstraintException extends \RuntimeException
{
    /** @var string[] */
    private $errors;

    public function __construct(array $errors)
    {
        parent::__construct();

        $this->errors = $errors;
    }

    /**
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
