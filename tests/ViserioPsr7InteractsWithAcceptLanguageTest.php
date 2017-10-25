<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use Viserio\Component\Http\ServerRequest;

class ViserioPsr7InteractsWithAcceptLanguageTest extends AbstractInteractsWithAcceptLanguageTest
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
