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

use Hearsay\PubSubHubbubBundle\Exception\SecurityException;
use Hearsay\PubSubHubbubBundle\Topic\TopicInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Helper to interact with a PubSubHubbub hub; manages subscription and polling
 * interactions with that hub.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class HubSubscriber {

    /**
     * @var UrlGeneratorInterface
     */
    private $generator = null;
    /**
     * Name of the route to use for callback URLs.
     * @var string
     */
    private $callbackRoute = null;
    /**
     * The URL of the hub we're subscribing/unsubscribing.
     * @var string
     */
    private $hubUrl = null;

    /**
     * Standard constructor.
     * @param string $hubUrl The URL of the hub we're subscribing/unsubscribing.
     * @param UrlGeneratorInterface $generator Helper to generate callback URLs.
     * @param string $callbackRoute Name of the callback route.
     */
    public function __construct($hubUrl, UrlGeneratorInterface $generator, $callbackRoute = "pubsubhubbub") {
        $this->hubUrl = $hubUrl;
        $this->generator = $generator;
        $this->callbackRoute = $callbackRoute;
    }

    /**
     * Get the URL of the hub we're interfacing with.
     * @return string The URL.
     */
    public function getHubUrl() {
        return $this->hubUrl;
    }

    /**
     * Get the callback URL which should be used for handling notifications and
     * verify requests related to the given topic.
     * @param TopicInterface The topic being processed.
     * @return string The callback URL.
     */
    protected function getCallbackUrl(TopicInterface $topic) {
        return $this->generator->generate($this->callbackRoute);
    }

    /**
     * Get a cURL handle for a request to our hub, with the given fields
     * provided as POST parameters.  Subclasses may wish to override to
     * set additional options on the handle.
     * @return resource The cURL handle to poll the hub with the given parameters.
     */
    protected function createRequestHandle() {
        $ch = \curl_init($this->getHubUrl());
        \curl_setopt_array($ch, array(
            \CURLOPT_POST => true,
            \CURLOPT_RETURNTRANSFER => true,
        ));
        return $ch;
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
            "hub.callback" => $this->getCallbackUrl($topic),
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

    /**
     * Get the POST fields needed in a subscription request for the given topic.
     * @param TopicInterface $topic The topic being subscribed to.
     * @return array The POST fields.
     */
    protected function getSubscribeFields(TopicInterface $topic) {
        $fields = $this->getCommonPostFields($topic);
        $fields["hub.mode"] = "subscribe";

        return $fields;
    }

    /**
     * Register to receive updates on a topic.
     * @param TopicInterface $topic The topic to subscribe to.
     */
    public function subscribe(TopicInterface $topic) {
        $ch = $this->getRequestHandle();
        \curl_setopt($ch, \CURLOPT_POSTFIELDS, $this->getSubscribeFields($topic));
    }

}
