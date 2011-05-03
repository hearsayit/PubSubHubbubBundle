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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Setup for the PubSubHubbub bundle extension.
 * @package HearsayPubSubHubbubBundle
 * @subpackage DependencyInjection
 * @author Kevin Montag <kevin@hearsay.it>
 */
class HearsayPubSubHubbubExtension extends Extension {

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $processor = new Processor();

        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);
        $loader->load('hub.xml');

        // Set the hub URL
        $container->setParameter('hearsay_pubsubhubbub.hub_url', $config['hub']);

        // Use the configured service as the handler
        $container->setAlias('hearsay_pubsubhubbub.notification_handler', $config['handler']);

        $extensions = array();

        // We always use the core component
        $extensions[] = new Reference('hearsay_pubsubhubbub.core_component');
        $container->setParameter('hearsay_pubsubhubbub.host', $config['core']['host']);
        $container->setParameter('hearsay_pubsubhubbub.base_url', $config['core']['base_url']);
        $container->setParameter('hearsay_pubsubhubbub.scheme', $config['core']['scheme']);

        // Add the superfeedr component if it's been configured
        if (isset($config['superfeedr'])) {
            $container->setParameter('hearsay_pubsubhubbub.superfeedr_username', $config['superfeedr']['username']);
            $container->setParameter('hearsay_pubsubhubbub.superfeedr_password', $config['superfeedr']['password']);

            $extensions[] = new Reference('hearsay_pubsubhubbub.superfeedr_component');
        }

        // TODO: Other extensions
        // Add the extensions and factory arguments to our hub
        $container->getDefinition('hearsay_pubsubhubbub.hub')->addArgument($extensions);
        $container->getDefinition('hearsay_pubsubhubbub.hub')->addArgument(new Reference('hearsay_pubsubhubbub.curl_factory'));

        // Set up the provider
        if (isset($config['provider']['service'])) {
            $container->setAlias('hearsay_pubsubhubbub.topic_provider', $config['provider']['service']);
        } else if (isset($config['provider']['doctrine'])) {
            $doctrineConfig = $config['provider']['doctrine'];
            $definition = $container->getDefinition('hearsay_pubsubhubbub.doctrine_topic_provider');
            $definition->addArgument(new Reference($doctrineConfig['manager']));
            $definition->addArgument($doctrineConfig['entity']);
            $definition->addArgument($doctrineConfig['property']);
            $container->setAlias('hearsay_pubsubhubbub.topic_provider', 'hearsay_pubsubhubbub.doctrine_topic_provider');
        }
    }

}
