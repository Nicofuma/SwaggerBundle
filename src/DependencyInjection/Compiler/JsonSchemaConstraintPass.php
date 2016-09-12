<?php

namespace Nicofuma\SwaggerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JsonSchemaConstraintPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $factoryDefinition = $container->getDefinition('swagger.json_schema.constraints.factory');

        $services = $container->findTaggedServiceIds('swagger.json_schema.constraint');
        foreach ($services as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $constaintDefinition = $container->findDefinition($serviceId);

                $factoryDefinition->addMethodCall('setConstraintClass', [$attributes['constraint'], $constaintDefinition->getClass()]);
            }
        }
    }
}
