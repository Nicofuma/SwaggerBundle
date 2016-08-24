<?php

namespace SwaggerValidationBundle;

use SwaggerValidationBundle\DependencyInjection\Compiler\FOSRestPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SwaggerValidationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FOSRestPass());
    }
}
