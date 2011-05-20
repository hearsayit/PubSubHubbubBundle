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

namespace Hearsay\PubSubHubbubBundle\Topic;

/**
 * Dummy topic which should only be used for debugging.  Allows arbitrary
 * subscription/unsubscription.  Its ID is just its URL.
 * @package HearsayPubSubHubbubBundle
 * @subpackage Topic
 * @author Kevin Montag <kevin@hearsay.it>
 */
class BasicTopic implements TopicInterface {

    /**
     * @var string
     */
    private $url = null;
    
    /**
     * Standard constructor.
     * @param string $url The topic URL.
     */
    public function __construct($url) {
        $this->url = $url;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTopicId() {
        return $this->url;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTopicUrl() {
        return $this->url;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTopicSecret() {
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isSubscribeAllowed(){
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isUnsubscribeAllowed() {
        return true;
    }
}
