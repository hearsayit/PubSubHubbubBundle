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

namespace Hearsay\PubSubHubbubBundle\Test;

use Hearsay\PubSubHubbubBundle\Events;
use Hearsay\PubSubHubbubBundle\Event\SubscriptionEvent;
use Hearsay\PubSubHubbubBundle\Hub\HubSubscriberInterface;
use Hearsay\PubSubHubbubBundle\Topic\TopicInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * A hub subscriber implementation which just dispatches events when it is told
 * to subscribe or unsubscribe.  Useful for test environments where it is 
 * desirable to suppress actual subscriptions.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class DummyHubSubscriber implements HubSubscriberInterface {

    /**
     * The event dispatcher.
     * @var EventDispatcherInterface
     */
    private $dispatcher = null;

    /**
     * Standard constructor.
     * @param EventDispatcherInterface $dispatcher Dispatcher to use for sending
     * subscribe and unsubscribe events.
     */
    public function __construct(EventDispatcherInterface $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Get the dispatcher to use for sending subscribe and unsubscribe events.
     * @return EventDispatcherInterface The dispatcher.
     */
    protected function getEventDispatcher() {
        return $this->dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe(TopicInterface $topic, array $options = array()) {
        $event = new SubscriptionEvent($topic, $options);
        $this->getEventDispatcher()->dispatch(Events::onTestSubscribeRequest, $event);
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe(TopicInterface $topic, array $options = array()) {
        $event = new SubscriptionEvent($topic, $options);
        $this->getEventDispatcher()->dispatch(Events::onTestUnsubscribeRequest, $event);
        return '';
    }

}
