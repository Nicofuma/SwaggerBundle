<?php

namespace Nicofuma\SwaggerBundle\Validator;

use Nicofuma\SwaggerBundle\Exception\NoValidatorException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ValidatorMap
{
    protected $container;
    protected $map;

    public function __construct(ContainerInterface $container, array $map)
    {
        $this->container = $container;
        $this->map = $map;
    }

    /**
     * @param Request $request
     *
     * @return Validator
     */
    public function getValidator(Request $request)
    {
        foreach ($this->map as $validatorId => $requestMatcher) {
            if (null === $requestMatcher || $requestMatcher->matches($request)) {
                return $this->container->get($validatorId);
            }
        }

        throw new NoValidatorException();
    }
}
