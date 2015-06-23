<?php

/*
 * (c) Rob Bast <rob.bast@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Alcohol\PasteBundle\Tests\Integration;

use Alcohol\PasteBundle\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @medium
 */
class CreateControllerTest extends WebTestCase
{
    /**
     * @inheritDoc
     */
    public static function createKernel(array $options = array())
    {
        return new Application(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }

    /**
     * @group integration
     */
    public function testPostRaw()
    {
        $client = static::createClient();
        $client->request('POST', '/', [], [], [], 'Lorem ipsum');

        $this->assertEquals(
            201,
            $client->getResponse()->getStatusCode(),
            '"POST /" should return a 201 Created response.'
        );

        $this->assertTrue(
            $client->getResponse()->headers->has('Location'),
            '"POST /" response should include a Location header.'
        );

        $this->assertTrue(
            $client->getResponse()->headers->has('X-Paste-Token'),
            '"POST /" response should include a X-Paste-Token header.'
        );
    }

    /**
     * @group integration
     */
    public function testPostRawFail()
    {
        $client = static::createClient();
        $client->request('POST', '/', [], [], [], '');

        $this->assertEquals(
            400,
            $client->getResponse()->getStatusCode(),
            '"POST /" without input should return a 400 Bad Request.'
        );
    }

    /**
     * @group integration
     */
    public function testPostForm()
    {
        $client = static::createClient();
        $client->request('POST', '/', ['paste' => 'Lorem ipsum']);

        $this->assertEquals(
            201,
            $client->getResponse()->getStatusCode(),
            '"POST /" should return a 201 Created response.'
        );

        $this->assertTrue(
            $client->getResponse()->headers->has('Location'),
            '"POST /" response should include a Location header.'
        );

        $this->assertTrue(
            $client->getResponse()->headers->has('X-Paste-Token'),
            '"POST /" response should include a X-Paste-Token header.'
        );
    }

    /**
     * @group integration
     */
    public function testPostFormFail()
    {
        $client = static::createClient();
        $client->request('POST', '/', ['paste' => '']);

        $this->assertEquals(
            400,
            $client->getResponse()->getStatusCode(),
            '"POST /" without input should return a 400 Bad Request.'
        );
    }
}