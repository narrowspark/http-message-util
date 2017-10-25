<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use Narrowspark\Http\Message\Util\InteractsWithAuthorization;
use PHPUnit\Framework\TestCase;

abstract class AbstractInteractsWithAuthorizationTest extends TestCase
{
    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    public function testBasicAuthorization(): void
    {
        $request = $this->request->withHeader('Authorization', 'Basic QWxhZGRpbjpPcGVuU2VzYW1l');

        self::assertSame(
            [
                'Basic',
                'QWxhZGRpbjpPcGVuU2VzYW1l',
            ],
            InteractsWithAuthorization::getAuthorization($request)
        );
    }
}
