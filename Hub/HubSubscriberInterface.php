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
 * Interface for a helper to subscribe to or unsubscribe from topics.
 * @author Kevin Montag <kevin@hearsay.it>
 */
interface HubSubscriberInterface {

    /**
     * Subscribe to the given topic.
     * @param TopicInterface $topic The topic.
     * @param array $options Any additional options to provide to the request.
     * @return string The server's response.
     * @throws SubscriptionNotVerifiedException
     */
    public function subscribe(TopicInterface $topic, array $options = array());

    /**
     * Unsubscribe from the given topic.
     * @param TopicInterface $topic The topic.
     * @param array $options Any additional options to provide to the request.
     * @return string The server's response.
     * @throws SubscriptionNotVerifiedException
     */
    public function unsubscribe(TopicInterface $topic, array $options = array());
    
    
}
