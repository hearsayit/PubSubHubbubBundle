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

use Hearsay\PubSubHubbubBundle\Topic\TopicInterface;

/**
 * Interface for hub components.  Components are objects which understand how
 * to build requests to send to a PubSubHubbub hub, based on provided options.
 * Many components can be attached to a hub object in an additive way.  Thus,
 * for example, a component concerned with an extension to the PubSubHubbub
 * spec should not worry about implementing the core spec as well, but rather
 * should assume that the component implementing the core spec will be used
 * in addition.
 * @package HearsayPubSubHubbubBundle
 * @subpackage Hub
 * @author Kevin Montag <kevin@hearsay.it>
 */
interface HubComponentInterface {

    /**
     * Get an array of [name] => [default value] pairs for the options that may
     * be passed to this component for requests of the given mode.
     * @param string $mode The request mode (e.g. the value of the hub.mode
     * parameter).
     * @return array The allowed options for this component, with their default
     * values.
     */
    public function getOptions(Hub $hub, $mode);

    /**
     * Get any component-specific POST parameters which should be attached to
     * requests of the given mode.
     * @param string $mode The hub.mode parameter for the request.
     * @param array $options The options passed in for the request.
     * @return array The component-specific POST fields, as [key] => [value]
     * pairs.
     */
    public function getParameters(Hub $hub, $mode, array $options);

    /**
     * Directly modify a hub request before it is sent.  Should be used to add
     * things like authentication information and additional headers to the
     * request.
     * @param string $mode The hub.mode parameter for the request.
     * @param array $options The options passed in to the request.
     * @param resource $ch The cURL handle for the request.
     */
    public function modifyRequest(Hub $hub, $mode, array $options, resource $ch);
}