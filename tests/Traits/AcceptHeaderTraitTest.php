<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use Narrowspark\Http\Message\Util\Traits\AcceptHeaderTrait;
use PHPUnit\Framework\TestCase;

class AcceptHeaderTraitTest extends TestCase
{
    use AcceptHeaderTrait;

    public function testGetAcceptableContentTypes(): void
    {
        self::assertEquals(
            ['application/vnd.wap.wmlscriptc', 'text/vnd.wap.wml', 'application/vnd.wap.xhtml+xml', 'application/xhtml+xml', 'text/html', 'multipart/mixed', '*/*'],
            self::getHeaderValuesFromString('application/vnd.wap.wmlscriptc, text/vnd.wap.wml, application/vnd.wap.xhtml+xml, application/xhtml+xml, text/html, multipart/mixed, */*')
        );

        self::assertEquals([], self::getHeaderValuesFromString('')); // testing caching
    }
}
