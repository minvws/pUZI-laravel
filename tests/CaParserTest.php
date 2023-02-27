<?php

namespace MinVWS\PUZI\Laravel\Tests;

use Illuminate\Support\Facades\App;
use MinVWS\PUZI\Laravel\CaParser;
use MinVWS\PUZI\Laravel\UziServiceProvider;

class UziServiceProviderTest extends TestCase
{
    public function testCACertsEmpty(): void
    {
        $this->assertEmpty(CaParser::getCertsFromFile(null));
        $this->assertEmpty(CaParser::getCertsFromFile(''));
    }

    public function testCACerts(): void
    {
        $caCerts = CaParser::getCertsFromFile(__DIR__ . '/Resources/test-fake-ca-file.pem');

        $this->assertCount(2, $caCerts);
        $this->assertSame([
            'Some certificate data....',
            'Some other certificate data....',
        ], $caCerts);
    }
}
