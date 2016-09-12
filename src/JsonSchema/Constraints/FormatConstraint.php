<?php

namespace Nicofuma\SwaggerBundle\JsonSchema\Constraints;

use JsonSchema\Constraints\FormatConstraint as FormatConstraintBase;
use Nicofuma\SwaggerBundle\Exception\FormatConstraintException;
use Nicofuma\SwaggerBundle\JsonSchema\Constraints\Format\FormatValidatorInterface;

class FormatConstraint extends FormatConstraintBase
{
    /** @var FormatValidatorInterface[] */
    private $formatMap = [];

    /**
     * {@inheritdoc}
     */
    public function check($element, $schema = null, $path = null, $i = null)
    {
        if (!isset($schema->format)) {
            return;
        }

        if (array_key_exists($schema->format, $this->formatMap)) {
            try {
                $this->formatMap[$schema->format]->validate($element);
            } catch (FormatConstraintException $e) {
                foreach ($e->getErrors() as $error) {
                    $this->addError($path, $error, 'format');
                }
            }

            return;
        }

        parent::check($element, $schema, $path, $i);
    }

    /**
     * Add a format validator.
     *
     * @param string                   $format
     * @param FormatValidatorInterface $formatValidator
     */
    public function addFormatValidator($format, FormatValidatorInterface $formatValidator)
    {
        $this->formatMap[$format] = $formatValidator;
    }
}
