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
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Unit tests for the callback controller.
 * @package HearsayPubSubHubbubBundle
 * @subpackage Tests
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
     * Mock logger, initialized for each test.
     * @var \Symfony\Bundle\MonologBundle\Logger\Logger
     */
    protected $logger = null;
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
     * Get a service container which just contains the given request as its
     * 'request' service.
     * @return Container The container.
     */
    private function getContainer(Request $request) {
        $container = new Container();
        $container->set('request', $request);
        return $container;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp() {
        $this->topicProvider = $this->getMock('Hearsay\PubSubHubbubBundle\Topic\TopicProviderInterface');
        $this->notificationHandler = $this->getMock('Hearsay\PubSubHubbubBundle\Handler\NotificationHandlerInterface');
        $this->logger = $this->getMockBuilder('Symfony\Bundle\MonologBundle\Logger\Logger')
                        ->disableOriginalConstructor()
                        ->getMock();
        $this->topic = $this->getMock('Hearsay\PubSubHubbubBundle\Topic\TopicInterface');
        $this->topicProvider
                ->expects($this->any())
                ->method("getTopic")
                ->with($this->identifier)
                ->will($this->returnValue($this->topic));
        $this->topic
                ->expects($this->any())
                ->method("getTopicId")
                ->will($this->returnValue($this->identifier));
        
        // Fix to force detection of mocked methods
        $this->topicProvider->getTopic($this->identifier);
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
                ->method("getTopicSecret")
                ->will($this->returnValue("secret"));

        // We should get a notification
        $this->notificationHandler
                ->expects($this->once())
                ->method("handle")
                ->with($this->topic, $request->headers, "Whatever");

        $controller = new CallbackController($this->topicProvider, $this->notificationHandler, $this->getContainer($request), $this->logger);
        $response = $controller->callbackAction($this->identifier);

        // And we should see a successful response
        $this->assertTrue($response->isSuccessful());
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
                ->method("getTopicSecret")
                ->will($this->returnValue("not null"));

        // We should never be notified
        $this->notificationHandler
                ->expects($this->never())
                ->method("handle");

        $controller = new CallbackController($this->topicProvider, $this->notificationHandler, $this->getContainer($request), $this->logger);
        $response = $controller->callbackAction($this->identifier);

        // And we should silently fail
        $this->assertTrue($response->isNotFound());
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
                ->method("getTopicSecret")
                ->will($this->returnValue("good"));

        // We should never be notified
        $this->notificationHandler
                ->expects($this->never())
                ->method("handle");

        $controller = new CallbackController($this->topicProvider, $this->notificationHandler, $this->getContainer($request), $this->logger);
        $response = $controller->callbackAction($this->identifier);

        // And we should silently fail
        $this->assertTrue($response->isNotFound());
    }

    /**
     * Make sure subscription or unsubscription requests are allowed when
     * they're supposed to be.
     * @covers Hearsay\PubSubHubbubBundle\Controller\CallbackController
     */
    public function testGoodSubscriptionVerified() {
        $request = Request::create('/pubsubhubbub?hub.mode=subscribe&' .
                        'hub.topic=http://rss.topic.com&' .
                        'hub.challenge=print_this&' .
                        'hub.lease_seconds=100');

        $this->topic
                ->expects($this->any())
                ->method('getTopicUrl')
                ->will($this->returnValue('http://rss.topic.com/'));

        $this->topic
                ->expects($this->any())
                ->method('isSubscribeAllowed')
                ->will($this->returnValue(true));

        // We shouldn't receive a notification
        $this->notificationHandler
                ->expects($this->never())
                ->method('handle');

        $controller = new CallbackController($this->topicProvider, $this->notificationHandler, $this->getContainer($request), $this->logger);
        $response = $controller->callbackAction($this->identifier);

        // And we should be successful
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('print_this', $response->getContent());
    }
    
    /**
     * Make sure that we allow unsubscribe requests for topics which we don't
     * recognize.
     * @covers Hearsay\PubSubHubbubBundle\Controller\CallbackController
     */
    public function testUnknownTopicUnsubscribes() {
        $request = Request::create('/pubsubhubbub?hub.mode=unsubscribe&' .
                'hub.topic=http://rss.topic.com&' .
                'hub.challenge=print_this');
        
        // Mock provider will not find a topic with our ID
        $provider = $this->getMock('Hearsay\PubSubHubbubBundle\Topic\TopicProviderInterface');
        $provider
                ->expects($this->once())
                ->method('getTopic')
                ->with('unknown-id')
                ->will($this->returnValue(null));
        
        // We shouldn't receive a notification
        $this->notificationHandler
                ->expects($this->never())
                ->method('handle');
       
        $controller = new CallbackController($provider, $this->notificationHandler, $this->getContainer($request), $this->logger);
        $response = $controller->callbackAction('unknown-id');
        
        // And we should be successful
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('print_this', $response->getContent());
    }

}
