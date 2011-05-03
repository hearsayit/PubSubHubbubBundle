<?php
/*
 * Copyright 2011 Hearsay.
 */

namespace Hearsay\PubSubHubbubBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to subscribe or unsubscribe from a topic on a PubSubHubbub hub.
 * @author Kevin Montag
 */
class SubscribeCommand extends Command {

    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this
                ->setDefinition(array(
                    new InputArgument('topic_id', InputArgument::REQUIRED, 'ID of the topic to subscribe to/unsubscribe from'),
                ))
                ->setDescription('Subscribe to (or unsubscribe from) to a PubSubHubbub topic.')
                ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'The host to use in generating callback URLs')
                ->addOption('base', null, InputOption::VALUE_OPTIONAL, 'The base URL to use in generating callback URLs')
                ->addOption('scheme', null, InputOption::VALUE_OPTIONAL, 'The scheme (e.g. https) to use in generating callback URLs')
                ->addOption('unsubscribe', 'u', InputOption::VALUE_NONE,
                        'Unsubscribe from the topic instead of subscribing')
                ->setHelp(<<<EOT
The <info>pubsubhubbub:subscribe</info> command controls a subscription to a
topic in a PubSubHubbub hub.  It can be used both for subscribing and
unsubscribing.

The topic will be retrieved (using the specified ID) from the topic provider
defined in the PubSubHubbub bundle's configuration.
EOT
                        )
                ->setName('pubsubhubbub:subscribe');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        // Set up the base URL
        $context = $this->container->get('router')->getContext();
        
        if ($input->getOption('base')) {
            $context->setBaseUrl($input->getOption('base'));
        }
        if ($input->getOption('host')) {
            $context->setHost($input->getOption('host'));
        }
        if ($input->getOption('scheme')) {
            $context->setScheme($input->getOption('scheme'));
        }

        $subscriber = $this->container->get('hearsay_pubsubhubbub.hub_subscriber');
        $response = null;

        $subscribing = !($input->getOption('unsubscribe'));

        $topic = $this->container->get('hearsay_pubsubhubbub.topic_provider')->getTopic($input->getArgument('topic_id'));
        $text = $subscribing ? 'Subscribing to ' : 'Unsubscribing from ';
        $text .= 'topic URL ' . $topic->getUrl() . '...';
        $output->writeln($text);

        if ($subscribing) {
            $response = $subscriber->subscribe($topic);
        } else {
            $response = $subscriber->unsubscribe($topic);
        }

        $output->writeln('Finished, server response was:');
        $output->writeln($response);
    }
}
