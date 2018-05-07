<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use Viserio\Component\Http\Response;

class ViserioPsr7InteractsWithDispositionTest extends AbstractInteractsWithContentTypesTest
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        if (! \class_exists(Response::class)) {
            $this->markTestSkipped('Run composer req --dev viserio/http to test this test.');
        }

        parent::setUp();

        $this->request = new Response();
    }
}
