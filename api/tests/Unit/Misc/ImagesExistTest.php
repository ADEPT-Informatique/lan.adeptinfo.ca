<?php

namespace Tests\Unit\Misc;

use GuzzleHttp\Client;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class ImagesExistTest extends TestCase
{
    use DatabaseMigrations;

    public function testBannerImageExist(): void
    {
        $client = new Client();
        $image = $client->get(env('BANNER_URL'))->getBody();
        $this->assertEquals(file_get_contents(base_path().'/resources/views/emails/assets/banner.jpg'), $image->getContents());
    }

    public function testFooterImageExist(): void
    {
        $client = new Client();
        $image = $client->get(env('FOOTER_URL'))->getBody();
        $this->assertEquals(file_get_contents(base_path().'/resources/views/emails/assets/footer.jpg'), $image->getContents());
    }
}
