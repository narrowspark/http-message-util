<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use GuzzleHttp\Psr7\Response;

class GuzzlePsr7InteractsWithDispositionTest extends AbstractInteractsWithDispositionTest
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->response = new Response();
    }
}
