<?php

namespace Nicofuma\SwaggerBundle;

use Nicofuma\SwaggerBundle\DependencyInjection\Compiler\FormatValidatorPass;
use Nicofuma\SwaggerBundle\DependencyInjection\Compiler\FOSRestPass;
use Nicofuma\SwaggerBundle\DependencyInjection\Compiler\JsonSchemaConstraintPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NicofumaSwaggerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FOSRestPass());
        $container->addCompilerPass(new FormatValidatorPass());
        $container->addCompilerPass(new JsonSchemaConstraintPass());
    }
}
