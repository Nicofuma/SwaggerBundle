<?php

namespace Nicofuma\SwaggerBundle\JsonSchema\Constraints\Format;

use Nicofuma\SwaggerBundle\Exception\FormatConstraintException;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UUIDValidator implements FormatValidatorInterface
{
    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator = null)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value)
    {
        if ($this->validator !== null) {
            $errorList = $this->validator->validate($value, [new Uuid()]);

            if (count($errorList) > 0) {
                $errors = [];

                /** @var ConstraintViolation $error */
                foreach ($errorList as $error) {
                    $errors[] = $error->getMessage();
                }

                throw new FormatConstraintException($errors);
            }
        }
    }
}
