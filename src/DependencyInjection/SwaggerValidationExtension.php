<?php

namespace SwaggerValidationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class SwaggerValidationExtension extends Extension
{
    private $requestMatchers = [];

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $mapDefinition = $container->getDefinition('swagger_validation.validator_map');
        $map = [];
        foreach ($config['definitions'] as $key => $definition) {
            $matcher = $this->createRequestMatcher(
                $container,
                $definition['pattern']['path'],
                $definition['pattern']['host'],
                $definition['pattern']['methods'],
                $definition['pattern']['ips']
            );

            $schemaManagerId = 'swagger_validation.schema_manager.'.$key;
            $schemaManager = $container->setDefinition($schemaManagerId, new DefinitionDecorator('swagger_validation.schema_manager'));
            $schemaManager->replaceArgument(0, $definition['swagger_file']);

            $validatorId = 'swagger_validation.validator.'.$key;
            $validator = $container->setDefinition($validatorId, new DefinitionDecorator('swagger_validation.validator'));
            $validator->replaceArgument(0, new Reference($schemaManagerId));
            $validator->replaceArgument(1, $definition['strict']);

            $map[$validatorId] = $matcher;
        }
        $mapDefinition->replaceArgument(1, $map);
    }

    private function createRequestMatcher(ContainerBuilder $container, $path = null, $host = null, $methods = [], $ip = null, array $attributes = [])
    {
        if ($methods) {
            $methods = array_map('strtoupper', (array) $methods);
        }

        $serialized = serialize([$path, $host, $methods, $ip, $attributes]);
        $id = 'swagger_validation.request_matcher.'.md5($serialized).sha1($serialized);

        if (isset($this->requestMatchers[$id])) {
            return $this->requestMatchers[$id];
        }

        // only add arguments that are necessary
        $arguments = [$path, $host, $methods, $ip, $attributes];
        while (count($arguments) > 0 && !end($arguments)) {
            array_pop($arguments);
        }

        $container
            ->register($id, RequestMatcher::class)
            ->setPublic(false)
            ->setArguments($arguments)
        ;

        return $this->requestMatchers[$id] = new Reference($id);
    }
}
