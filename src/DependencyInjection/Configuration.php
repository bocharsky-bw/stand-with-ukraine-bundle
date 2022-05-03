<?php

namespace BW\StandWithUkraineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('stand_with_ukraine');

        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->arrayNode('banner')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        # TODO Idea to add left/right positions and render a small banner on sides
                        ->scalarNode('position')
                            ->defaultValue('top')
                            ->info('Possition of the banner: "top" or "bottom"')
                            ->validate()
                                ->ifNotInArray([
                                    'top',
                                    'bottom',
                                ])
                                ->thenInvalid('Valid values are: "top" or "bottom"')
                            ->end()
                        ->end()
                        ->scalarNode('target_url')
                            ->defaultNull()
                            ->info('Wrap the banner with a link to the given URL')
                        ->end()
                        ->scalarNode('brand_name')
                            ->defaultNull()
                            ->info('Will be shown in the banner, HTTP host by default')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('ban_language')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('use_links')->defaultTrue()->end()
                        // TODO Possible options
                        //->booleanNode('censorship')->defaultTrue()->end()
                        //->booleanNode('polite')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('ban_country')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->booleanNode('use_links')->defaultTrue()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
