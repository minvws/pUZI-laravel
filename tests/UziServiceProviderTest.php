<?php

namespace MinVWS\PUZI\Laravel\Tests;

use Illuminate\Support\Facades\App;
use MinVWS\PUZI\Laravel\UziServiceProvider;

class UziServiceProviderTest extends TestCase
{
    public function testCACertsEmpty(): void
    {
        $serviceProvider = new UziServiceProvider(app());

        $this->assertEmpty($serviceProvider->getCACerts(null));
        $this->assertEmpty($serviceProvider->getCACerts(''));
    }

    public function testCACerts(): void
    {
        $serviceProvider = new UziServiceProvider(app());

        $caCerts = $serviceProvider->getCACerts(__DIR__ . '/Resources/test-fake-ca-file.pem');

        $this->assertCount(2, $caCerts);
        $this->assertSame([
            'Some certificate data....',
            'Some other certificate data....',
        ], $caCerts);
    }
}
