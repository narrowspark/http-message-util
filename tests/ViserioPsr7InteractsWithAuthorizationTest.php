<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use Viserio\Component\Http\Request;
use Viserio\Component\Http\ServerRequest;

class ViserioPsr7InteractsWithAuthorizationTest extends AbstractInteractsWithAuthorizationTest
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        if (! \class_exists(Request::class)) {
            $this->markTestSkipped('Run composer req --dev viserio/http to test this test.');
        }

        parent::setUp();

        $this->request = new ServerRequest('/');
    }
}
