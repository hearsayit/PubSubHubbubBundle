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

namespace Hearsay\PubSubHubbubBundle\Hub;

/**
 * Object representing a PubSubHubbub hub, with any number of supported
 * components for interaction with that hub.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class Hub {

    /**
     * The hub URL.
     * @var string
     */
    private $url = null;

    /**
     * Components for handling requests to the hub.
     * @var array
     */
    private $components = null;

    /**
     * Standard constructor.
     * @param string $url The hub URL.
     * @param array $components The components to use for building requests to
     * the hub.
     */
    public function __construct($url, array $components) {
        $this->url = $url;
        $this->components = $components;
    }

    /**
     * Get the hub URL.
     * @return string Hub URL.
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Add a component for interaction with the hub.
     * @param HubComponentInterface $component The component to add.
     */
    public function addComponent(HubComponentInterface $component) {
        $this->components[] = $component;
    }

    /**
     * Get the list of hub components.
     * @return array The hub components.
     */
    protected function getComponents() {
        return $this->components;
    }

    /**
     * Send a request to this hub.
     * @param string $mode The request type, e.g. the value of the hub.mode
     * POST parameter.
     * @param array $options The options to pass in to the hub components for
     * creating the request.
     * @return string The request response.
     */
    public function makeRequest($mode, array $options) {
        // Set up the request
        $ch = \curl_init($this->getHubUrl());
        \curl_setopt_array($ch, array(
            \CURLOPT_POST => true,
            \CURLOPT_RETURNTRANSFER => true,
        ));

        // Build up the request via components
        // TODO: Detect conflicts?
        $fields = array();
        foreach ($this->getComponents() as $component) {
            // Get component-specific options from the global list, falling back on component defaults
            $componentOptions = $component->getOptions($this, $mode);
            foreach ($options as $option => $value) {
                if (isset($componentOptions[$option])) {
                    $componentOptions[$option] = $value;
                }
            }

            // Merge in the component-specific fields
            $fields = \array_merge($fields, $component->getParameters($this, $mode, $componentOptions));

            // Let the component modify the request handle
            $component->modifyRequest($this, $mode, $componentOptions);
        }

        // Add the fields to the request
        \curl_setopt($ch, \CURLOPT_POSTFIELDS, $fields);

        // Execute it
        $response = \curl_exec($ch);

        return $response;
    }

}
