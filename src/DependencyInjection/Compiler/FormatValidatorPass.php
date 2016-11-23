<?php

namespace Nicofuma\SwaggerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FormatValidatorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $constraintFactoryDefinition = $container->getDefinition('swagger.json_schema.constraints.factory');
        $formatConstraintDefinition = $container->getDefinition('swagger.json_schema.constraints.format');

        $services = $container->findTaggedServiceIds('swagger.format_validator');
        foreach ($services as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $formatConstraintDefinition->addMethodCall('addFormatValidator', [$attributes['format'], new Reference($serviceId)]);
                $constraintFactoryDefinition->addMethodCall('setFormatValidator', [$attributes['format'], new Reference($serviceId)]);
            }
        }
    }
}
