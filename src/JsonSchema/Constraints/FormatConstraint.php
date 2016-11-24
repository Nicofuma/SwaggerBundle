<?php

namespace Nicofuma\SwaggerBundle\JsonSchema\Constraints;

use JsonSchema\Constraints\Factory as BaseFactory;
use JsonSchema\Constraints\FormatConstraint as FormatConstraintBase;
use JsonSchema\Uri\UriRetriever;
use Nicofuma\SwaggerBundle\Exception\FormatConstraintException;
use Nicofuma\SwaggerBundle\JsonSchema\Constraints\Format\FormatValidatorInterface;

class FormatConstraint extends FormatConstraintBase
{
    /** @var FormatValidatorInterface[] */
    private $formatMap = [];

    public function __construct($checkMode, UriRetriever $uriRetriever, BaseFactory $factory)
    {
        parent::__construct($checkMode, $uriRetriever, $factory);

        if ($factory instanceof \Nicofuma\SwaggerBundle\JsonSchema\Constraints\Factory) {
            $this->formatMap = $factory->getFormatValidators();
        }
    }

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
