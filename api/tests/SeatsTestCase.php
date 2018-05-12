<?php

namespace Tests;

use Seatsio\SeatsioClient;

abstract class SeatsTestCase extends TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function setUp()
    {
        parent::setUp();

        $seatsClient = new SeatsioClient(env('SECRET_KEY_ID'));
        $seatsClient->events()->release(env('EVENT_KEY_ID'), ["A-1"]);
    }

    public function tearDown()
    {
        $seatsClient = new SeatsioClient(env('SECRET_KEY_ID'));
        $seatsClient->events()->release(env('EVENT_KEY_ID'), ["A-1"]);

        parent::tearDown();
    }
}
