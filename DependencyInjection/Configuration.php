<?php

namespace Loevgaard\PakkelabelsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('loevgaard_pakkelabels');

        $rootNode
            ->children()
                ->scalarNode('api_username')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('api_password')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
