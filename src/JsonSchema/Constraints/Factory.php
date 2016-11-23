<?php

namespace Nicofuma\SwaggerBundle\JsonSchema\Constraints;

class Factory extends \JsonSchema\Constraints\Factory
{
    /** @var Format\FormatValidatorInterface[] */
    private $formatValidators;

    /**
     * Add a format validator.
     *
     * @param string $format
     * @param Format\FormatValidatorInterface $validator
     */
    public function setFormatValidator($format, Format\FormatValidatorInterface $validator)
    {
        $this->formatValidators[$format] = $validator;
    }

    /**
     * @return Format\FormatValidatorInterface[]
     */
    public function getFormatValidators()
    {
        return $this->formatValidators;
    }
}
