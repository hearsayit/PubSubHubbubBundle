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

namespace Hearsay\PubSubHubbubBundle\Subscription;

use Hearsay\PubSubHubbubBundle\Exception\SecurityException;
use Hearsay\PubSubHubbubBundle\Topic\TopicInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Service for subscribing to and unsubscribing from topics in a PubSubHubbub
 * hub.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class HubSubscriber {

    /**
     * The URL of the hub we're subscribing/unsubscribing.
     * @var string
     */
    private $hubUrl;

    public function __construct(UrlGeneratorInterface $generator, $hubUrl) {
        
    }

    /**
     * Get the URL of the hub we're subscribing/unsubscribing.
     * @return string The URL.
     */
    public function getHubUrl() {
        return $this->hubUrl;
    }

    /**
     * Get an array of post fields which are common to both subscribe and
     * unsubscribe requests.
     * @param TopicInterface $topic The topic being subscribed or unsubscribed.
     * @return array The post fields for the action.
     */
    private function getCommonPostFields(TopicInterface $topic) {
        $fields = array(
            "hub.verify" => "sync",
            "hub.topic" => $topic->getTopic(),
        );

        $secret = $topic->getSecret();
        if ($secret) {
            // Must connect securely to use a topic secret
            $scheme = \parse_url($this->getHubUrl(), \PHP_URL_SCHEME);
            if ($scheme === "https") {
                $fields["hub.secret"] = $secret;
            } else {
                throw new SecurityException("Hub secret values may only be sent over a secure connection.");
            }
        }

        return $fields;
    }

    protected function getSubscribePostFields(TopicInterface $topic) {
        $fields = $this->getCommonPostFields($topic);
        $fields["hub.mode"] = "subscribe";

        return $fields;
    }

    protected function getSubscribeHandle(TopicInterface $topic) {
        $ch = \curl_init($this->hubUrl);
        \curl_setopt_array($ch, array(
            \CURLOPT_POST => true,
            \CURLOPT_POSTFIELDS => array(
                "hub.mode" => "subscribe",
                "hub.verify" => "sync",
                "hub.topic" => $topic->getTopic(),
                "hub.callback" => null,
            ),
        ));
    }

    /**
     * Register to receive updates on a topic.
     * @param TopicInterface $topic The topic to subscribe to.
     */
    public function subscribe(TopicInterface $topic) {
        
    }

}
