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

/**
 * Hub component allowing for interaction with the Superfeedr hub.
 * @link http://superfeedr.com/
 * @package HearsayPubSubHubbubBundle
 * @subpackage Hub
 * @author Kevin Montag <kevin@hearsay.it>
 */
class SuperfeedrComponent extends AbstractHubComponent {

    /**
     * @var string
     */
    private $username = null;
    /**
     * @var string
     */
    private $password = null;
    /**
     * Default value for the digest option.
     * @var bool
     */
    private $digestDefault = null;

    /**
     * Standard constructor.
     * @param string $username Superfeedr username.
     * @param string $password Superfeedr password.
     * @param bool $digestDefault Whether to subscribe for digest on feeds by default.
     */
    public function __construct($username, $password, $digestDefault = false) {
        $this->username = $username;
        $this->password = $password;
        $this->digestDefault = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(Hub $hub, $mode) {        
        if ($mode == "subscribe" || $mode == "unsubscribe") {
            return array(
                "digest" => $this->digestDefault,
            );
        } else {
            return parent::getOptions($hub, $mode);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(Hub $hub, $mode, array $options) {
        $parameters = array();
        if ($mode == "subscribe") {
            $parameters["superfeedr.digest"] = $options["digest"];
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyRequest(Hub $hub, $mode, array $options, resource $ch) {
        // Use HTTP basic authentication for the request
        \curl_setopt($ch, \CURLOPT_HTTPAUTH, \CURLAUTH_BASIC);
        \curl_setopt($ch, \CURLOPT_USERPWD, "$this->username:$this->password");
    }
}
