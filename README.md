This is a Symfony2 bundle which helps your application act as a subscriber to
a PubSubHubbub hub.  Supports 

Installation
============

Add the bundle to your vendors directory.
-----------------------------------------

    $ git submodule add git@github.com:hearsayit/PubSubHubbubBundle.git src/vendors/bundles/Hearsay/PubSubHubbubBundle

Register the Hearsay namespace with the autoloader.
---------------------------------------------------
    
    // app/autoload.php

    $loader->registerNamespaces(array(
        // ...

        'Hearsay' => __DIR__.'/../vendor/bundles',

        // ...
    ));

Add the bundle to your kernel.
------------------------------

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...

            new Hearsay\PubSubHubbubBundle\HearsayPubSubHubbubBundle(),

            // ...
        );
    }

Configure the bundle.
---------------------

The minimal configuration just takes a hub URL:

    hearsay_pub_sub_hubbub:
        hub:           http://hub.com

You'll likely also want to configure your callback URL, since this can't be
reliably determined when e.g. subscribing from the command line:

    hearsay_pub_sub_hubbub:
        # ...
        core:
            host:      your.app.com
            base_url:  /your_app_subdirectory
            scheme:    http

You can specify a service implementing `Hearsay\PubSubHubbubBundle\Topic\TopicProviderInterface`
to control your allowed topic subscriptions (the default provider allows subscriptions to
or unsubscriptions from any topic URL, so it's not at all secure):

    hearsay_pub_sub_hubbub:
        # ...
        provider:
            service:   your_service_id

A Doctrine provider is included if your topics are database entities:

    hearsay_pub_sub_hubbub:
        # ...
        provider:
            doctrine:
                entity:     \Your\Topic\Entity    # Must implement Hearsay\PubSubHubbubBundle\Topic\TopicInterface

You can specify a service implementing `Hearsay\PubSubHubbubBundle\Handler\NotificationHandlerInterface`
to handle push notifications as they arrive (the default handler dispatches
onPushNotificationReceived events):

    hearsay_pub_sub_hubbub:
        # ...
        handler:      your_handler_service

Connecting to the Superfeedr hub requires some additional information:

    hearsay_pub_sub_hubbub:
        # ...
        superfeedr:
            username: superfeedr_username
            password: superfeedr_password
            digest:   true      # Whether to receive daily digest notifications (false by default)

Subscribe to topics.
--------------------

To subscribe or unsubscribe from a topic from the command line (topic_id is the
ID passed to your topic provider; if no provider is configured, this is just
the topic URL):

    app$ ./console pubsubhubbub:subscribe [-u] topic_id

Or, from a controller:

    $this->get('hearsay_pubsubhubbub.hub_subscriber')->subscribe($topicId);
    $this->get('hearsay_pubsubhubbub.hub_subscriber')->unsubscribe($topicId);