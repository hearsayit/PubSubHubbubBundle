<?php

/* Copyright (c) 2008 James Socol

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in
  all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
  THE SOFTWARE.
 */

namespace Hearsay\PubSubHubbubBundle\Tests\Web;

use Hearsay\PubSubHubbubBundle\Web\Curl;

/**
 * Test functions for OOCurl
 *
 * The structure of the test is essentially this:
 *  # Create a new Curl object.
 *  # Retrieve a URL.
 *  # Parse some data from it.
 *  # If there's a failure at any point, throw an Exception.
 *
 * Modified from the original for inclusion in the Hearsay PubSubHubbub bundle.
 *
 * This test does require a working internet connection. But if
 * you don't have one, why are you using cURL?
 *
 * @package OOCurl
 * @author James Socol <me@jamessocol.com>
 * @copyright Copyright (c) 2008, James Socol
 * @version 0.1.0
 * @license http://www.opensource.org/licenses/mit-license.php
 */
class CurlTest extends \PHPUnit_Framework_TestCase {

    /**
     * Standard OOCurl tests, adapted from the originals.
     * @link https://github.com/jsocol/oocurl/blob/master/test.php
     */
    public function testCurl() {

        // First make sure we have an internet connection
        try {
            \fsockopen("www.google.com", 80);
        } catch (\Exception $e) {
            $this->markTestSkipped("Can't test cURL without an internet connection.");
        }

        $curl = new Curl;

        // If something happened, (which it shouldn't) throw an exception
        if (!$curl)
            throw new Exception("I couldn't create a Curl object. Was PHP compiled with cURL?");

        /**
         * Set the URL for $curl to our test location:
         */
        $curl->url = "http://jamessocol.com/projects/oocurl_test.php";

        /**
         * Set a custom header for the test.
         */
        $curl->httpheader = array('X-OOCurl-Version: ' . Curl::VERSION);

        /**
         * Execute the HTTP query
         */
        $response = $curl->exec();

        // If there's no response, there was an error.
        if (!$response)
            throw new Exception("I couldn't fetch the response.");
    }

}
