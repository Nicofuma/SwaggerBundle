<?php

namespace Nicofuma\SwaggerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nicofuma_swagger', 'array');

        $rootNode
            ->fixXmlConfig('definition')
            ->children()
                ->arrayNode('definitions')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('pattern')
                                ->beforeNormalization()->ifString()->then(function ($v) {
                                    return ['path' => $v];
                                })->end()
                                ->fixXmlConfig('ip')
                                ->fixXmlConfig('method')
                                ->children()
                                    ->scalarNode('path')
                                        ->defaultNull()
                                        ->info('use the urldecoded format')
                                     ->example('^/path to resource/')
                                    ->end()
                                    ->scalarNode('host')->defaultNull()->end()
                                    ->arrayNode('ips')
                                        ->beforeNormalization()->ifString()->then(function ($v) {
                                            return [$v];
                                        })->end()
                                        ->prototype('scalar')->end()
                                    ->end()
                                    ->arrayNode('methods')
                                        ->beforeNormalization()->ifString()->then(function ($v) {
                                            return preg_split('/\s*,\s*/', $v);
                                        })->end()
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('swagger_file')->end()
                            ->booleanNode('strict')->defaultTrue()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
