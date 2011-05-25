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

namespace Hearsay\PubSubHubbubBundle\Event;

use Hearsay\PubSubHubbubBundle\Topic\TopicInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event signaling that a subscribe or unsubscribe request has been made.
 * @package HearsayPubSubHubbubBundle
 * @subpackage Event
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SubscriptionEvent extends Event {

    /**
     * The topic which is the subject of the request.
     * @var TopicInterface
     */
    private $topic = null;

    /**
     * The options passed along with the request.
     * @var array
     */
    private $options = null;
    
    /**
     * Standard constructor.
     * @param TopicInterface $topic The topic which is the subject of the
     * request.
     * @param array $options The options passed with the subscription request.
     */
    public function __construct(TopicInterface $topic, array $options) {
        $this->topic = $topic;
        $this->options = $options;
    }

    /**
     * Get the topic which is the subject of the request.
     * @return TopicInterface The topic.
     */
    public function getTopic() {
        return $this->topic;
    }

    /**
     * Get the options which were passed with the request.
     * @return array The options.
     */
    public function getOptions() {
        return $this->options;
    }
}
