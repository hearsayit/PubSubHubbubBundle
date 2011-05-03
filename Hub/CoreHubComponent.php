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

use Hearsay\PubSubHubbubBundle\Exception\BadOptionException;
use Hearsay\PubSubHubbubBundle\Exception\SecurityException;
use Hearsay\PubSubHubbubBundle\Topic\TopicInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Component providing subscribe/unsubscribe functionality for the bundle.
 * Interacts with hubs implementing the core PubSubHubbub spec.
 * @link http://pubsubhubbub.googlecode.com/svn/trunk/pubsubhubbub-core-0.3.html
 * @package HearsayPubSubHubbubBundle
 * @subpackage Hub
 * @author Kevin Montag <kevin@hearsay.it>
 */
class CoreHubComponent extends AbstractHubComponent {

    /**
     * Helper to generate callback URLs.
     * @var UrlGenerator
     */
    private $generator = null;

    /**
     * Name of the callback route.
     * @var string
     */
    private $callbackRoute = null;

    /**
     * Standard constructor.
     * @param Router $router Router containing the callback route.
     * @param RequestContext $context The context to use for generating URLs.
     * @param string $callbackRoute The route which should be used as a callback
     * for hub requests.
     */
    public function __construct(Router $router, RequestContext $context, $callbackRoute = "pubsubhubbub") {
        $this->generator = new UrlGenerator($router->getRouteCollection(), $context);
        $this->callbackRoute = $callbackRoute;
    }

    /**
     * Get the callback URL which should be used for handling notifications and
     * verifying requests related to the given topic.
     * @param TopicInterface The topic being processed.
     * @return string The callback URL.
     */
    protected function getCallbackUrl(TopicInterface $topic) {
        return $this->generator->generate($this->callbackRoute, array(
            'topicId' => $topic->getTopicId(),
                ), true);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(Hub $hub, $mode) {
        if ($mode == "subscribe" || $mode == "unsubscribe") {
            return array(
                "topic" => null,
            );
        } else {
            return parent::getOptions($hub, $mode);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(Hub $hub, $mode, array $options) {
        // We only know how to process subscription requests
        if ($mode == "subscribe" || $mode == "unsubscribe") {
            $topic = $options["topic"];
            if (!($topic instanceof TopicInterface)) {
                throw new BadOptionException('Core hub component requires an ' .
                        'instance of Hearsay\PubSubHubbubBundle\Topic\TopicInterface ' .
                        'as the "topic" option for subscription requests.');
            } else {
                $fields = array(
                    "hub.verify" => "sync",
                    "hub.topic" => $topic->getTopicUrl(),
                    "hub.callback" => $this->getCallbackUrl($topic),
                );

                $secret = $topic->getTopicSecret();
                if ($secret) {
                    // Must connect securely to use a topic secret
                    $scheme = \parse_url($hub->getUrl(), \PHP_URL_SCHEME);
                    if ($scheme === "https") {
                        $fields["hub.secret"] = $secret;
                    } else {
                        throw new SecurityException("Hub secret values may only be sent over a secure connection.");
                    }
                }
                return $fields;
            }
        } else {
            return array();
        }
    }

}
