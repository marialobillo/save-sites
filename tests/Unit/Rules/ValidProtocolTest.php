<?php

namespace Tests\Unit\Rules;

use PHPUnit\Framework\TestCase;
use App\Rules\ValidProtocol;

class ValidProtocolTest extends TestCase
{
    
    /** @test */
    public function it_only_allows_http_or_https()
    {
        $validProtocol = new ValidProtocol;

        $this->assertTrue($validProtocol->passes('url', 'https://google.com'));
        $this->assertTrue($validProtocol->passes('url', 'http://google.com'));
        $this->assertFalse($validProtocol->passes('url', 'httpsgoogle.com'));
        $this->assertFalse($validProtocol->passes('url', 'https:google.com'));
        $this->assertFalse($validProtocol->passes('url', 'ftp://google.com'));
        $this->assertFalse($validProtocol->passes('url', 'https:/google.com'));
        // $this->assertFalse($validProtocol->passes('url', 'googlehttps://.com'));

    }
}
