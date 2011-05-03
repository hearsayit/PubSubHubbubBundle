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

use Hearsay\PubSubHubbubBundle\Exception\SubscriptionNotVerifiedException;
use Hearsay\PubSubHubbubBundle\Topic\TopicInterface;

/**
 * Simple helper to manage subscriptions on a PubSubHubbub hub which implements
 * the core spec.
 * @package HearsayPubSubHubbubBundle
 * @subpackage Hub
 * @author Kevin Montag <kevin@hearsay.it>
 */
class HubSubscriber {

    /**
     * The hub we're subscribing/unsubscribing from.
     * @var Hub
     */
    private $hub = null;

    /**
     * Standard constructor.
     * @param Hub $hub The hub to subscribe/unsubscribe.
     */
    public function __construct(Hub $hub) {
        $this->hub = $hub;
    }

    /**
     * Get the hub to which subscriptions are being applied.
     * @return Hub The hub.
     */
    protected function getHub() {
        return $this->hub;
    }

    /**
     * Subscribe to the given topic.
     * @param TopicInterface $topic The topic.
     * @param array $options Any additional options to provide to the request.
     * @return string The server's response.
     */
    public function subscribe(TopicInterface $topic, array $options = array()) {
        return $this->makeSubscriptionRequest('subscribe', $topic, $options);        
    }

    private function makeSubscriptionRequest($mode, TopicInterface $topic, array $options = array()) {
        $options['topic'] = $topic;
        $curl = $this->getHub()->makeRequest($mode, $options);

	// Check the response code
	if ($curl->info('http_code') !== 204) {
	  throw new SubscriptionNotVerifiedException('Server returned code ' . $curl->info('http_code') . ' with response: "' . $curl->fetch() . '"');
	}

	return $curl->fetch();
    }

    /**
     * Unsubscribe from the given topic.
     * @param TopicInterface $topic The topic.
     * @param array $options Any additional options to provide to the request.
     * @return string The server's response.
     */
    public function unsubscribe(TopicInterface $topic, array $options = array()) {
        return $this->makeSubscriptionRequest('unsubscribe', $topic, $options);
    }
}
