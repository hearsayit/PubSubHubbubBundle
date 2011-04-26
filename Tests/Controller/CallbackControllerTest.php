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

namespace Hearsay\PubSubHubbubBundle\Tests\Controller;

use Hearsay\PubSubHubbubBundle\Controller\CallbackController;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Unit tests for the callback controller.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class CallbackControllerTest extends \PHPUnit_Framework_TestCase {

    /**
     * Mock topic provider, initialized for each test.
     * @var \Hearsay\PubSubHubbubBundle\Topic\TopicProviderInterface
     */
    protected $topicProvider = null;
    /**
     * Mock notification handler, initialized for each test.
     * @var \Hearsay\PubSubHubbubBundle\Handler\NotificationHandlerInterface
     */
    protected $notificationHandler = null;
    /**
     * Mock topic, initialized for each test.
     * @var \Hearsay\PubSubHubbubBundle\Topic\TopicInterface
     */
    protected $topic = null;
    /**
     * Identifier which can be used to retrieve the mock topic from the mock
     * topic provider.
     * @var string
     */
    protected $identifier = "id";

    /**
     * {@inheritdoc}
     */
    protected function setUp() {
        $this->topicProvider = $this->getMock('Hearsay\PubSubHubbubBundle\Topic\TopicProviderInterface');
        $this->notificationHandler = $this->getMock('Hearsay\PubSubHubbubBundle\Handler\NotificationHandlerInterface');
        $this->topic = $this->getMock('Hearsay\PubSubHubbubBundle\Topic\TopicInterface');
        $this->topicProvider
                ->expects($this->any())
                ->method("getTopic")
                ->with($this->identifier)
                ->will($this->returnValue($this->topic));
        $this->topic
                ->expects($this->any())
                ->method("getIdentifier")
                ->will($this->returnValue($this->identifier));
    }

    /**
     * Make sure a properly authenticated push notification is handled
     * with a success response.
     * @covers Hearsay\PubSubHubbubBundle\Controller\CallbackController
     */
    public function testAuthenticatedNotificationProcessed() {
        $signature = \hash_hmac("sha1", "Whatever", "secret");

        // Sanity check for the test; should be a 40-byte hex string as per the spec
        $this->assertEquals(40, \strlen($signature));

        // Request with authentication info
        $request = new Request(array(), array(), array(), array(), array(), array(), "Whatever");
        $request->headers = new HeaderBag(array(
                    "Content-Type" => "contentType",
                    "X-Hub-Signature" => "sha1=" . $signature,
                ));

        // And a topic that has an authentication secret
        $this->topic
                ->expects($this->any())
                ->method("getSecret")
                ->will($this->returnValue("secret"));

        // We should get a notification
        $this->notificationHandler
                ->expects($this->once())
                ->method("handle")
                ->with($this->topic, "contentType", "Whatever");

        $controller = new CallbackController($this->topicProvider, $this->notificationHandler, $request);
        $response = $controller->callbackAction($this->identifier);

        // And we should see a successful response
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Make sure that if no authentication information is provided with a
     * request when a topic secret is provided, the request is silently
     * ignored.
     * @covers Hearsay\PubSubHubbubBundle\Controller\CallbackController
     */
    public function testUnauthenticatedNotificationIgnored() {
        // Request with no authentication info
        $request = new Request(array(), array(), array(), array(), array(), array(), "Some content");
        $request->headers = new HeaderBag(array(
                    "Content-Type" => "contentType",
                ));

        // But the topic has authentication info
        $this->topic
                ->expects($this->any())
                ->method("getSecret")
                ->will($this->returnValue("not null"));

        // We should never be notified
        $this->notificationHandler
                ->expects($this->never())
                ->method("handle");

        $controller = new CallbackController($this->topicProvider, $this->notificationHandler, $request);
        $response = $controller->callbackAction($this->identifier);

        // And we should silently fail
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Make sure that push notifications with incorrect authentication are
     * silently ignored.
     * @covers Hearsay\PubSubHubbubBundle\Controller\CallbackController
     */
    public function testIncorrectAuthenticationIgnored() {
        // Request with bad authentication info
        $badSignature = \hash_hmac("sha1", "Weird", "bad");
        $request = new Request(array(), array(), array(), array(), array(), array(), "Some content");
        $request->headers = new HeaderBag(array(
                    "Content-Type" => "contentType",
                    "X-Hub-Signature" => "sha1=" . $badSignature,
                ));

        // Topic has different authentication info
        $this->topic
                ->expects($this->any())
                ->method("getSecret")
                ->will($this->returnValue("good"));

        // We should never be notified
        $this->notificationHandler
                ->expects($this->never())
                ->method("handle");

        $controller = new CallbackController($this->topicProvider, $this->notificationHandler, $request);
        $response = $controller->callbackAction($this->identifier);

        // And we should silently fail
        $this->assertEquals(404, $response->getStatusCode());
    }

}
