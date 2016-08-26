<?php

namespace Nicofuma\SwaggerBundle;

use Nicofuma\SwaggerBundle\DependencyInjection\Compiler\FOSRestPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NicofumaSwaggerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FOSRestPass());
    }
}
