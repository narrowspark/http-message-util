<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use Narrowspark\Http\Message\Util\InteractsWithDisposition;
use PHPUnit\Framework\TestCase;

abstract class AbstractInteractsWithDispositionTest extends TestCase
{
    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMakeDispositionInvalidDisposition(): void
    {
        InteractsWithDisposition::makeDisposition('invalid', 'foo.html');
    }

    /**
     * @dataProvider provideMakeDisposition
     *
     * @param string $disposition
     * @param string $filename
     * @param string $filenameFallback
     * @param string $expected
     */
    public function testMakeDisposition(string $disposition, string $filename, string $filenameFallback, string $expected): void
    {
        $response = InteractsWithDisposition::appendDispositionHeader($this->response, $disposition, $filename, $filenameFallback);

        self::assertEquals($expected, $response->getHeaderLine('Content-Disposition'));
    }

    public function provideMakeDisposition(): array
    {
        return [
            ['attachment', 'foo.html', 'foo.html', 'attachment; filename=foo.html'],
            ['attachment', 'foo.html', '', 'attachment; filename=foo.html'],
            ['attachment', 'foo bar.html', '', 'attachment; filename="foo bar.html"'],
            ['attachment', 'foo "bar".html', '', 'attachment; filename="foo \\"bar\\".html"'],
            ['attachment', 'foo%20bar.html', 'foo bar.html', 'attachment; filename="foo bar.html"; filename*=utf-8\'\'foo%2520bar.html'],
            ['attachment', 'föö.html', 'foo.html', 'attachment; filename=foo.html; filename*=utf-8\'\'f%C3%B6%C3%B6.html'],
        ];
    }

    /**
     * @dataProvider provideMakeDispositionFail
     * @expectedException \InvalidArgumentException
     *
     * @param string $disposition
     * @param string $filename
     */
    public function testMakeDispositionFail(string $disposition, string $filename): void
    {
        InteractsWithDisposition::appendDispositionHeader($this->response, $disposition, $filename);
    }

    public function provideMakeDispositionFail(): array
    {
        return [
            ['attachment', 'foo%20bar.html'],
            ['attachment', 'foo/bar.html'],
            ['attachment', '/foo.html'],
            ['attachment', 'foo\bar.html'],
            ['attachment', '\foo.html'],
            ['attachment', 'föö.html'],
        ];
    }

    public function testEncodedFallbackFilename(): void
    {
        self::assertSame('', InteractsWithDisposition::encodedFallbackFilename(''));
        self::assertSame('f___.html', InteractsWithDisposition::encodedFallbackFilename('fööö.html'));

        $iso88591EncodedFilename = \utf8_decode('föö.html');
        self::assertSame('f__.html', InteractsWithDisposition::encodedFallbackFilename($iso88591EncodedFilename));
        self::assertSame('fooo.html', InteractsWithDisposition::encodedFallbackFilename('fooo.html'));
        self::assertSame('fo__.html', InteractsWithDisposition::encodedFallbackFilename('foöä.html'));
    }
}
