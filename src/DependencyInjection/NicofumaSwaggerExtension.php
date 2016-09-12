<?php

namespace Nicofuma\SwaggerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class NicofumaSwaggerExtension extends Extension
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

        $mapDefinition = $container->getDefinition('swagger.validator_map');
        $map = [];
        foreach ($config['definitions'] as $key => $definition) {
            $matcher = $this->createRequestMatcher(
                $container,
                $definition['pattern']['path'],
                $definition['pattern']['host'],
                $definition['pattern']['methods'],
                $definition['pattern']['ips']
            );

            $schemaManagerId = 'swagger.schema_manager.'.$key;
            $schemaManager = $container->setDefinition($schemaManagerId, new DefinitionDecorator('swagger.schema_manager'));
            $schemaManager->replaceArgument(0, $definition['swagger_file']);

            $validatorId = 'swagger.validator.'.$key;
            $validator = $container->setDefinition($validatorId, new DefinitionDecorator('swagger.validator'));
            $validator->replaceArgument(1, new Reference($schemaManagerId));
            $validator->replaceArgument(2, $definition['strict']);

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
        $id = 'swagger.request_matcher.'.md5($serialized).sha1($serialized);

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
