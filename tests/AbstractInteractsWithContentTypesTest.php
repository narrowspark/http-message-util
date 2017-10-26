<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use Narrowspark\Http\Message\Util\InteractsWithContentTypes;
use PHPUnit\Framework\TestCase;

abstract class AbstractInteractsWithContentTypesTest extends TestCase
{
    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    public function testIsJson(): void
    {
        $request = $this->request->withHeader('Content-Type', 'application/json, */*');

        self::assertTrue(InteractsWithContentTypes::isJson($request));

        $request = $request->withoutHeader('Content-Type');

        self::assertFalse(InteractsWithContentTypes::isJson($request));
    }

    public function testIsPjax(): void
    {
        $request = $this->request->withHeader('x-pjax', 'true');

        self::assertTrue(InteractsWithContentTypes::isPjax($request));

        $request = $request->withoutHeader('x-pjax');

        self::assertFalse(InteractsWithContentTypes::isPjax($request));
    }

    public function testIsAjax(): void
    {
        $request = $this->request->withHeader('x-requested-with', 'XMLHttpRequest');

        self::assertTrue(InteractsWithContentTypes::isAjax($request));
    }

    public function testFormatReturnsAcceptsJson(): void
    {
        $request = $this->request->withHeader('Accept', 'application/json');

        self::assertTrue(InteractsWithContentTypes::accepts(['application/json'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['application/baz+json'], $request));

        $request = $this->request->withHeader('Accept', 'application/foo+json');

        self::assertTrue(InteractsWithContentTypes::accepts(['application/foo+json'], $request));
        self::assertFalse(InteractsWithContentTypes::accepts(['application/bar+json'], $request));
        self::assertFalse(InteractsWithContentTypes::accepts(['application/json'], $request));

        $request = $this->request->withHeader('Accept', 'application/*');

        self::assertTrue(InteractsWithContentTypes::accepts(['application/xml'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['application/json'], $request));
    }

    public function testFormatReturnsAcceptsHtml(): void
    {
        $request = $this->request->withHeader('Accept', 'text/html');

        self::assertTrue(InteractsWithContentTypes::accepts(['text/html'], $request));

        $request = $this->request->withHeader('Accept', 'text/*');

        self::assertTrue(InteractsWithContentTypes::accepts(['text/html'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['text/plain'], $request));
    }

    public function testFormatReturnsAcceptsAll(): void
    {
        $request = $this->request->withHeader('Accept', '*/*');

        self::assertTrue(InteractsWithContentTypes::accepts(['text/html'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['foo/bar'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['application/baz+xml'], $request));

        $request = $this->request->withHeader('Accept', '*');

        self::assertTrue(InteractsWithContentTypes::accepts(['text/html'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['foo/bar'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['application/baz+xml'], $request));
    }

    public function testFormatReturnsAcceptsMultiple(): void
    {
        $request = $this->request->withHeader('Accept', 'application/json,text/*');

        self::assertTrue(InteractsWithContentTypes::accepts(['text/html', 'application/json'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['text/html'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['text/foo'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['application/json'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['application/baz+json'], $request));
    }

    public function testFormatReturnsAcceptsCharset(): void
    {
        $request = $this->request->withHeader('Accept', 'application/json; charset=utf-8');

        self::assertTrue(InteractsWithContentTypes::accepts(['text/html', 'application/json'], $request));
        self::assertFalse(InteractsWithContentTypes::accepts(['text/html'], $request));
        self::assertFalse(InteractsWithContentTypes::accepts(['text/foo'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['application/json'], $request));
        self::assertTrue(InteractsWithContentTypes::accepts(['application/baz+json'], $request));
    }

    public function testBadAcceptHeader(): void
    {
        $request = $this->request->withHeader('Accept', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; pt-PT; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 (.NET CLR 3.5.30729)');

        self::assertFalse(InteractsWithContentTypes::accepts(['text/html', 'application/json'], $request));
        self::assertFalse(InteractsWithContentTypes::accepts(['text/html'], $request));
        self::assertFalse(InteractsWithContentTypes::accepts(['text/foo'], $request));
        self::assertFalse(InteractsWithContentTypes::accepts(['application/json'], $request));
        self::assertFalse(InteractsWithContentTypes::accepts(['application/baz+json'], $request));

        // Should not be handled as regex.
        $request = $this->request->withHeader('Accept', '.+/.+');

        self::assertFalse(InteractsWithContentTypes::accepts(['application/json'], $request));
        self::assertFalse(InteractsWithContentTypes::accepts(['application/baz+json'], $request));

        // Should not produce compilation error on invalid regex.
        $request = $this->request->withHeader('Accept', '(/(');

        self::assertFalse(InteractsWithContentTypes::accepts(['text/html'], $request));
    }

    public function testPrefers()
    {
        $request = $this->request->withHeader('Content-Type', 'application/json');

        self::assertEquals('json', InteractsWithContentTypes::prefers(['json'], $request));
        self::assertEquals('json', InteractsWithContentTypes::prefers(['html', 'json'], $request));

        $request = $this->request->withHeader('Content-Type', 'application/foo+json');

        self::assertEquals('application/foo+json', InteractsWithContentTypes::prefers(['application/foo+json'], $request));
        self::assertEquals('json', InteractsWithContentTypes::prefers(['json'], $request));

        $request = $this->request->withHeader('Content-Type', 'application/json;q=0.5, text/html;q=1.0');

        self::assertEquals('html', InteractsWithContentTypes::prefers(['json', 'html'], $request));

        $request = $this->request->withHeader('Content-Type', 'application/json;q=0.5, text/plain;q=1.0, text/html;q=1.0');

        self::assertEquals('txt', InteractsWithContentTypes::prefers(['json', 'txt', 'html'], $request));

        $request = $this->request->withHeader('Content-Type', 'application/*');

        self::assertEquals('json', InteractsWithContentTypes::prefers(['json'], $request));
        self::assertEquals('application/json', InteractsWithContentTypes::prefers(['application/json'], $request));
        self::assertEquals('application/xml', InteractsWithContentTypes::prefers(['application/xml'], $request));
        self::assertNull(InteractsWithContentTypes::prefers(['text/html'], $request));

        $request = $this->request->withHeader('Content-Type', 'application/json; charset=utf-8');

        self::assertEquals('json', InteractsWithContentTypes::prefers(['json'], $request));
        self::assertEquals('application/json', InteractsWithContentTypes::prefers(['application/json'], $request));

        $request = $this->request->withHeader('Content-Type', 'application/xml; charset=utf-8');

        self::assertNull(InteractsWithContentTypes::prefers(['html', 'json'], $request));

        $request = $this->request->withHeader('Content-Type', 'application/json, text/html');

        self::assertEquals('json', InteractsWithContentTypes::prefers(['html', 'json'], $request));

        $request = $this->request->withHeader('Content-Type', 'application/json;q=0.4, text/html;q=0.6');

        self::assertEquals('html', InteractsWithContentTypes::prefers(['html', 'json'], $request));
        self::assertEquals('text/html', InteractsWithContentTypes::prefers(['text/html', 'application/json'], $request));
        self::assertEquals('text/html', InteractsWithContentTypes::prefers(['application/json', 'text/html'], $request));

        $request = $this->request->withHeader('Content-Type', 'application/json, text/html');

        self::assertEquals('application/json', InteractsWithContentTypes::prefers(['text/html', 'application/json'], $request));

        $request = $this->request->withHeader('Content-Type', '*/*; charset=utf-8');

        self::assertEquals('json', InteractsWithContentTypes::prefers(['json'], $request));

    }
}
