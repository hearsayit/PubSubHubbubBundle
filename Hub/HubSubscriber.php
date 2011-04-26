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

use Doctrine\ORM\EntityManager;
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
     * The URL of the hub we're subscribing/unsubscribing.
     * @var string
     */
    private $hubUrl = null;
    /**
     * @var UrlGeneratorInterface
     */
    private $generator = null;
    /**
     * The subscriber extensions to apply to requests.
     * @var array
     */
    private $extensions = null;
    /**
     * Name of the route to use for callback URLs.
     * @var string
     */
    private $callbackRoute = null;

    /**
     * Standard constructor.
     * @param string $hubUrl The URL of the hub we're subscribing/unsubscribing.
     * @param UrlGeneratorInterface $generator Helper to generate callback URLs.
     * @param string $callbackRoute Name of the callback route.
     */
    public function __construct($hubUrl, UrlGeneratorInterface $generator, array $extensions = array(), $callbackRoute = "pubsubhubbub") {
        $this->hubUrl = $hubUrl;
        $this->generator = $generator;
        $this->extensions = $extensions;
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
     * verifying requests related to the given topic.
     * @param TopicInterface The topic being processed.
     * @return string The callback URL.
     */
    protected function getCallbackUrl(TopicInterface $topic) {
        return $this->generator->generate($this->callbackRoute);
    }

    /**
     * Get the extensions which should be applied to requests.
     * @return array The extensions.
     */
    protected function getExtensions() {
        return $this->extensions;
    }

    /**
     * Get an array of post fields which are common to both subscribe and
     * unsubscribe requests.
     * @param TopicInterface $topic The topic being subscribed or unsubscribed.
     * @return array The basic POST fields for the subscription actions.
     */
    private function getCommonFields(TopicInterface $topic) {
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
     * Sign up to receive updates, or unsubscribe from updates, for a particular
     * topic on this hub.
     * @param bool $subscribe Whether to subscribe (true) or unsubscribe
     * (false).
     * @param TopicInterface $topic The topic to subscribe or unsubscribe.
     */
    public function changeSubscriptionState($subscribe, TopicInterface $topic) {
        // Set up the request
        $ch = \curl_init($this->getHubUrl());
        \curl_setopt_array($ch, array(
            \CURLOPT_POST => true,
            \CURLOPT_RETURNTRANSFER => true,
        ));

        $fields = $this->getCommonFields($topic);
        $fields["hub.mode"] = $subscribe ? "subscribe" : "unsubscribe";

        \curl_setopt($ch, \CURLOPT_POSTFIELDS, $fields);

        // Execute it
        $response = \curl_exec($ch);
    }

}
