<?php

namespace Nicofuma\SwaggerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class FOSRestPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('fos_rest.request.param_fetcher')) {
            $definition = $container->getDefinition('swagger.fos_rest.request.param_fetcher');
            $definition->setDecoratedService('fos_rest.request.param_fetcher');
            $definition->replaceArgument(0, new Reference('swagger.fos_rest.request.param_fetcher.inner'));
        }
    }
}
