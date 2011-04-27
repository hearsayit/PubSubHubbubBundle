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
    public function load(array $config, ContainerBuilder $configuration) {
        $loader = new XmlFileLoader($configuration, new FileLocator(__DIR__ . "/../Resources/config"));
        $loader->load("hub.xml");        
    }

}
