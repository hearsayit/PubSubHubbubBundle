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

namespace Hearsay\PubSubHubbubBundle\Handler;

/**
 * Interface for objects which can handle hub push notifications.
 * @author Kevin Montag <kevin@hearsay.it>
 */
interface NotificationHandlerInterface {

    /**
     * Respond to a push notification.  As per the PubSubHubbub spec,
     * implementing classes should avoid performing time-consuming operations
     * inside this function.
     * @param string $contentType The value of the Content-Type header of the
     * notification request.
     * @param string $content The body of the notification request.
     */
    public function handle($contentType, $content);
}
