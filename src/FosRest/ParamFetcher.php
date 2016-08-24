<?php

namespace SwaggerValidationBundle\FosRest;

use FOS\RestBundle\Request\ParamFetcherInterface;
use SwaggerValidationBundle\Exception\NoValidatorException;
use SwaggerValidationBundle\Validator\Validator;
use SwaggerValidationBundle\Validator\ValidatorMap;
use Symfony\Component\HttpFoundation\RequestStack;

class ParamFetcher implements ParamFetcherInterface
{
    /** @var ParamFetcherInterface */
    private $decorated;

    /** @var ValidatorMap */
    private $validatorMap;

    /** @var RequestStack */
    private $requestStack;

    /** @var Validator */
    private $currentValidator;

    /** @var \stdClass[] */
    private $currentSchema;

    private $usingSwagger = false;

    public function __construct(ParamFetcherInterface $decorated, ValidatorMap $validatorMap, RequestStack $requestStack)
    {
        $this->decorated = $decorated;
        $this->validatorMap = $validatorMap;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function setController($controller)
    {
        $request = $this->requestStack->getCurrentRequest();

        try {
            $this->currentValidator = $this->validatorMap->getValidator($request);
            $this->currentSchema = null;
            $this->usingSwagger = true;
        } catch (NoValidatorException $e) {
            $this->usingSwagger = false;
            $this->decorated->setController($controller);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $strict = null)
    {
        if ($this->usingSwagger) {
            $currentSchema = $this->getCurrentSchema();
            if (array_key_exists($name, $currentSchema)) {
                $default = isset($currentSchema[$name]->default) ? $currentSchema[$name]->default : null;

                return $this->requestStack->getCurrentRequest()->query->get($name, $default);
            } else {
                throw new \InvalidArgumentException(sprintf("No swagger definition for parameter '%s'.", $name));
            }
        } else {
            return $this->decorated->get($name, $strict);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function all($strict = false)
    {
        if ($this->usingSwagger) {
            $parameters = [];
            foreach ($this->getCurrentSchema() as $item) {
                $parameters[$item->name] = $this->get($item->name);
            }

            return $parameters;
        } else {
            return $this->decorated->all($strict);
        }
    }

    /**
     * @return \stdClass[]
     */
    private function getCurrentSchema()
    {
        $request = $this->requestStack->getCurrentRequest();
        $path = $request->getPathInfo();

        if ($this->currentSchema === null) {
            $schemaManager = $this->currentValidator->getSchemaManager();

            $this->currentSchema = [];
            if ($schemaManager->findPathInTemplates($path, $template, $params)) {
                $currentSchema = $schemaManager->getRequestQueryParameters($template, $request->getMethod());

                foreach ($currentSchema as $keySchema) {
                    $this->currentSchema[$keySchema->name] = $keySchema;
                }
            }
        }

        return $this->currentSchema;
    }
}
