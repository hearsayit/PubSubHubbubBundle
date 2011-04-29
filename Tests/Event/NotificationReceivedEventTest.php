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

namespace Hearsay\PubSubHubbubBundle\Tests\Event;

use Hearsay\PubSubHubbubBundle\Event\NotificationReceivedEvent;

/**
 * Simple unit tests for events dispatched on push notifications.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class NotificationReceivedEventTest extends \PHPUnit_Framework_TestCase {

    /**
     * Simple test to make sure the constructor appropriately stores info.
     * @covers Hearsay\PubSubHubbubBundle\Event\NoficationReceivedEvent
     */
    public function testDataStored() {
        $topic = $this->getMock('Hearsay\PubSubHubbubBundle\Topic\TopicInterface');
        $event = new NotificationReceivedEvent($topic, "what", "now");

        $this->assertEquals($topic, $event->getTopic());
        $this->assertEquals("what", $event->getContentType());
        $this->assertEquals("now", $event->getContent());
    }
}
