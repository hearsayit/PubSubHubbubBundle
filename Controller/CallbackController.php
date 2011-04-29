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

namespace Hearsay\PubSubHubbubBundle\Controller;

use Hearsay\PubSubHubbubBundle\Handler\NotificationHandlerInterface;
use Hearsay\PubSubHubbubBundle\Topic\TopicProviderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller service to handle requests from PubSubHubbub hubs.
 * @package HearsayPubSubHubbubBundle
 * @subpackage Controller
 * @author Kevin Montag <kevin@hearsay.it>
 */
class CallbackController {

    private $topicProvider = null;
    private $notificationHandler = null;
    private $container = null;

    /**
     * Standard constructor.
     * @param TopicProviderInterface $topicProvider
     * @param NotificationHandlerInterface $notificationHandler
     * @param Container $container
     */
    public function __construct(TopicProviderInterface $topicProvider, NotificationHandlerInterface $notificationHandler, Container $container) {
        $this->topicProvider = $topicProvider;
        $this->notificationHandler = $notificationHandler;
        $this->container = $container;
    }

    /**
     * Get the helper for retrieving topics based on ID.
     * @return TopicProviderInterface The provider.
     */
    protected function getTopicProvider() {
        return $this->topicProvider;
    }

    /**
     * Get the handler for PubSubHubbub push notifications.
     * @return NotificationHandlerInterface The handler.
     */
    protected function getNotificationHandler() {
        return $this->notificationHandler;
    }

    /**
     * Get the current HTTP request.
     * @return Request The request.
     */
    protected function getRequest() {
        return $this->container->get('request');
    }

    /**
     * Primary callback action for interactions with hubs.
     * @param mixed $topicId The identifier for the topic relevant to this
     * request.
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callbackAction($topicId) {
        // Get the topic for the request
        $topic = $this->getTopicProvider()->getTopic($topicId);

        // Post parameters
        $post = $this->getRequest()->request;

        $mode = $post->get("hub.mode");
        if ($mode == "subscribe" || $mode == "unsubscribe") {
            // This is a verification of a subscribe/unsubscribe request
            if (
                    ($mode == "subscribe" && $topic->isSubscribeAllowed()) ||
                    ($mode == "unsubscribe" && $topic->isUnsubscribeAllowed())) {

                // The request is allowed
                return new Response($post->get("hub.challenge"), 200);
            } else {
                // The request is not allowed
                return new Response("Not Found", 404);
            }
        } else {
            // Otherwise, this is a push notification
            $content = $this->getRequest()->getContent();
            $contentType = $this->getRequest()->headers->get("Content-Type");

            // If appropriate, verify the sender of the notification
            $secret = $topic->getTopicSecret();
            if ($secret !== null) {
                // Get the signature from the header, which has the form sha1=signature
                $header = $this->getRequest()->headers->get("X-Hub-Signature");
                $matches = array();
                \preg_match("/^sha1=(.+)/", $header, $matches);

                $provided = "";
                if (isset($matches[1])) {
                    $provided = $matches[1];
                }

                $hmac = \hash_hmac("sha1", $content, $secret);

                if ($provided !== $hmac) {
                    // Silently fail
                    return new Response("Not Found", 404);
                }
            }

            // If we're here, the notification is allowed
            $this->getNotificationHandler()->handle($topic, $contentType, $content);

            // TODO: X-On-Behalf-Of support
            return new Response("", 200);
        }
    }

}
