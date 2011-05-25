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

use Hearsay\PubSubHubbubBundle\Hub\HubSubscriberInterface;
use Hearsay\PubSubHubbubBundle\Topic\TopicInterface;

/**
 * A hub subscriber implementation which doesn't actually do anything.  Useful
 * for test environments where it is desirable to suppress actual subscriptions.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class DummyHubSubscriber implements HubSubscriberInterface {

    /**
     * {@inheritdoc}
     */
    public function subscribe(TopicInterface $topic, array $options = array()) {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function unsubscribe(TopicInterface $topic, array $options = array()) {
        return '';
    }

}
