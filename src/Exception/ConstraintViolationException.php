<?php

namespace Nicofuma\SwaggerBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationException extends \LogicException
{
    private $violations;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations()
    {
        return $this->violations;
    }
}
