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

namespace Hearsay\PubSubHubbubBundle\Tests\Hub;

/**
 * Unit tests for hub connections.
 * @author Kevin Montag <kevin@hearsay.it>
 */
class HubTest extends \PHPUnit_Framework_TestCase {

    /**
     * Make sure the hub passes any options provided during requests down to
     * its components, using component-specified defaults for options which are
     * not provided.
     */
    public function testOptionsProvidedToComponents() {
        // TODO: Decent way of mocking cURL functionality
    }
}
