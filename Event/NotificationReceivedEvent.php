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
 * Event signaling that a push notification has been received.
 * @package HearsayPubSubHubbubBundle
 * @subpackage Event
 * @author Kevin Montag <kevin@hearsay.it>
 */
class NotificationReceivedEvent extends Event {

    /**
     * @var TopicInterface
     */
    private $topic = null;
    /**
     * @var string
     */
    private $contentType = null;
    /**
     * @var string
     */
    private $content = null;

    /**
     * Standard constructor.
     * @param TopicInterface $topic The topic which received the notification.
     * @param string $contentType The content type of the notification.
     * @param string $content The content received.
     */
    public function __construct(TopicInterface $topic, $contentType, $content) {
        $this->topic = $topic;
        $this->contentType = $contentType;
        $this->content = $content;
    }

    /**
     * Get the topic receiving the notification.
     * @return TopicInterface The topic.
     */
    public function getTopic() {
        return $this->topic;
    }

    /**
     * Get the content type of the notification, as specified by the
     * notification's Content-Type header.
     * @return string The content type, e.g. application/atom+xml.
     */
    public function getContentType() {
        return $this->contentType;
    }

    /**
     * Get the raw content of the notification.
     * @return string The content of the notification.
     */
    public function getContent() {
        return $this->content;
    }

}
