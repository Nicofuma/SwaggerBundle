<?php

namespace Nicofuma\SwaggerBundle\EventListener;

use Nicofuma\SwaggerBundle\Exception\ConstraintViolationException;
use Nicofuma\SwaggerBundle\Exception\NoValidatorException;
use Nicofuma\SwaggerBundle\Validator\ValidatorMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidatorListener implements EventSubscriberInterface
{
    /** @var ValidatorMap */
    private $map;

    public function __construct(ValidatorMap $map)
    {
        $this->map = $map;
    }

    /**
     * Handles swgger validations.
     *
     * @param GetResponseEvent $event An GetResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        try {
            $request = $event->getRequest();
            $validator = $this->map->getValidator($request);
            $validator->validate($request);
        } catch (NoValidatorException $e) {
            // Do nothing
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $violationMessages = explode("\n", $e->getMessage());
            array_shift($violationMessages);
            array_pop($violationMessages);
            $violations = new ConstraintViolationList();

            foreach ($violationMessages as $violationMessage) {
                list($path, $message) = explode(' ', $violationMessage, 2);

                $violations->add(new ConstraintViolation(
                    $message,
                    $message,
                    [],
                    null,
                    $path,
                    null
                ));
            }

            throw new ConstraintViolationException($violations);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 128],
        ];
    }
}
