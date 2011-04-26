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
 * Interface for extensions to the basic hub subscriber, which allow us to
 * interact with hubs that implement extensions to the PubSubHubbub spec.
 * @author Kevin Montag <kevin@hearsay.it>
 */
interface HubSubscriberExtensionInterface {

    /**
     * Get an array of [name] => [default value] pairs for the options that may
     * be passed to this extension for subscribe or unsubscribe requests.
     * @return array
     */
    public function getOptions();

    /**
     * Get any additional POST parameters which should be attached to subscribe
     * or unsubscribe requests for hubs supporting this extension.
     * @param TopicInterface $topic The topic being subscribed to.
     * @param array $options The options passed in to the subscribe request.
     * @return array The additional parameters, as [key] => [value] pairs.
     */
    public function getAdditionalSubscriptionParameters(TopicInterface $topic, array $options);

    /**
     * Directly modify a subscribe or unsubscribe request before it is sent.
     * Should be used to add things like authentication information and
     * additional headers to the request.
     * @param resource $ch The cURL handle for the request.
     */
    public function modifySubscriptionRequest(resource $ch);
}
