This is a Symfony2 bundle which helps your application act as a subscriber to
a PubSubHubbub hub.  Supports subscription via Superfeedr.

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

Add the bundle routes.
----------------------

    # app/config/routing.yml

    # ...
    _pubsubhubbub:
        resource: @HearsayPubSubHubbubBundle/Resources/config/routing.yml

Configuration
=============

Set up a hub connection.
------------------------

The minimal configuration takes a hub URL and a callback specification:

    hearsay_pub_sub_hubbub:
        hub:           http://hub.com
        callback:    
            host:      your.app.com              # Must be explicitly specified
            base_url:  /your_app_subdirectory    # Defaults to empty
            scheme:    https                     # Defaults to http

The callback configuration is required to allow for consistent callback URLs no
matter how you subscribe to feeds (from the command line, from a web interface,
etc.).

Tell the bundle which topics it's allowed to subscribe to.
----------------------------------------------------------

You can specify a service implementing `Hearsay\PubSubHubbubBundle\Topic\TopicProviderInterface`
to control your allowed topic subscriptions (the default provider allows 
subscriptions to or unsubscriptions from any topic URL, so it's useful for 
testing but not at all secure):

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

Add your Superfeedr credentials if necessary.
---------------------------------------------

Connecting to the Superfeedr hub requires some additional information:

    hearsay_pub_sub_hubbub:
        # ...
        superfeedr:
            username: superfeedr_username
            password: superfeedr_password
            digest:   true      # Whether to receive daily digest notifications (false by default)

Start receiving push notifications!
===================================

Subscribe to topics.
--------------------

To subscribe or unsubscribe from a topic from the command line (topic_id is the
ID passed to your topic provider; if no provider is configured, this is just
the topic URL):

    app$ ./console pubsubhubbub:subscribe [-u] topic_id

Or, from a controller:

    $this->get('hearsay_pubsubhubbub.hub_subscriber')->subscribe($topicId);
    $this->get('hearsay_pubsubhubbub.hub_subscriber')->unsubscribe($topicId);
