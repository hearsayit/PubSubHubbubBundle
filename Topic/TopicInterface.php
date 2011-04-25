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
 * Interface implemented by objects which correspond to PubSubHubbub topics.
 * @author Kevin Montag <kevin@hearsay.it>
 */
interface TopicInterface {

    /**
     * Get the RSS or Atom URL for this object.
     * @return string The URL.
     */
    public function getTopic();

    /**
     * Get the secret token which should be used for verifying server requests
     * related to this topic.
     * @return string|null The secret token, or null if requests should not be
     * verified.
     */
    public function getSecret();
}
