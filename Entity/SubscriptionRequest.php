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

namespace Hearsay\PubSubHubbubBundle\Entity;

use Hearsay\PubSubHubbubBundle\Topic\TopicInterface;

/**
 * Persistent request to subscribe to or unsubscribe from a PubSubHubbub
 * topic.
 * @orm:Entity
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscriptionRequest {

    /**
     * Primary identifier.
     * @orm:Column(type="integer")
     * @orm:GeneratedValue
     * @orm:Id
     * @var int
     */
    private $id = null;
    /**
     * Whether this is a subscribe (true) or unsubscribe (false) request.
     * @orm:Column(type="boolean")
     * @var bool
     */
    private $subscribing = null;
    /**
     * The URL associated with the request.
     * @orm:Column(type="string")
     * @var string
     */
    private $topicUrl = null;
    /**
     * The identifier for the topic associated with the request.
     * @orm:Column(type="integer")
     * @var int
     */
    private $topicIdentifier = null;

    public function __construct($subscribing, TopicInterface $topic) {
        $this->subscribing = $subscribing;
        $this->topicUrl = $topic->getTopic();
        $this->topicIdentifier = $topic->getIdentifier();
    }

    public function isSubscribing() {
        return $this->subscribing;
    }

    public function getTopicUrl() {
        return $this->topicUrl;
    }

    public function getTopicIdentifier() {
        return $this->topicIdentifier;
    }
}
