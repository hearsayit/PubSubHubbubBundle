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

namespace Hearsay\PubSubHubbubBundle\Handler;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Simple notification handler which dispatches an event upon receiving a
 * notification.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class EventNotificationHandler implements NotificationHandlerInterface {

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher = null;

    /**
     * Standard constructor.
     * @param EventDispatcherInterface $dispatcher Dispatcher to use for
     * notifications.
     */
    public function __construct(EventDispatcherInterface $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($contentType, $content) {
    }
}
