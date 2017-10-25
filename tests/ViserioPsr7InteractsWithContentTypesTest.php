<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use Viserio\Component\Http\ServerRequest;

class ViserioPsr7InteractsWithContentTypesTest extends AbstractInteractsWithContentTypesTest
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->request = new ServerRequest('/');
    }
}
