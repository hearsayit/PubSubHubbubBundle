<?php

/*
 * This file is part of the Hearsay PubSubHubbub bundle.
 *
 * The Hearsay PubSubHubbub bundle is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License
 * as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The Hearsay PubSubHubbub bundle is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the Hearsay PubSubHubbub bundle.  If not, see
 * <http://www.gnu.org/licenses/>.
 */

namespace Hearsay\PubSubHubbubBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Configuration definitions for the PubSubHubbub bundle.
 * @package HearsayPubSubHubbubBundle
 * @subpackage DependencyInjection
 * @author Kevin Montag <kevin@hearsay.it>
 */
class Configuration implements ConfigurationInterface {

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder() {
        $builder = new TreeBuilder();
        $rootNode = $builder->root('hearsay_pubsubhubbub');

        $rootNode
            ->children()
                ->scalarNode('handler')
                    ->cannotBeEmpty()
                    ->defaultValue('hearsay_pubsubhubbub.event_handler')
                ->end()
                ->scalarNode('hub')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('core')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('host')
                            ->defaultValue('localhost')
                        ->end()
                        ->scalarNode('base_url')
                            ->defaultValue('')
                        ->end()
                        ->scalarNode('scheme')
                            ->defaultValue('http')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('superfeedr')
                    ->validate()
                        ->ifTrue( function($v) { return !(isset($v['username']) && isset($v['password'])); } )
                        ->thenInvalid("Must set both username and password to connect with Superfeedr.")
                    ->end()
                    ->children()
                        ->scalarNode('username')->end()
                        ->scalarNode('password')->end()
                        ->booleanNode('digest')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('provider')
                    ->validate()
                        ->ifTrue( function($v) { return (\count($v) > 1); } )
                        ->thenInvalid("Cannot specify more than one topic provider.")
                    ->end()
                    ->validate()
                        ->ifTrue( function($v) { return (\count($v) === 0); } )
                        ->thenInvalid("Must specify a topic provider.")
                    ->end()
                    ->children()
                        ->scalarNode('service')->end()
                        ->arrayNode('doctrine')
                            ->children()
                                ->scalarNode('entity')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->end()
                                ->scalarNode('manager')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->defaultValue('doctrine_orm.default_entity_manager')
                                    ->end()
                                ->scalarNode('property')
                                    ->cannotBeEmpty()
                                    ->defaultValue('id')
                                    ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
        ;

        return $builder;
    }
}
