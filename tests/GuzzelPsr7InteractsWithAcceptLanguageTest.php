<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use GuzzleHttp\Psr7\ServerRequest;

class GuzzelPsr7InteractsWithAcceptLanguageTest extends AbstractInteractsWithAcceptLanguageTest
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->request = new ServerRequest('GET', '/');
    }
}
