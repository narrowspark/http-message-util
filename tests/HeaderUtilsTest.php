<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use Narrowspark\Http\Message\Util\HeaderUtils;
use PHPUnit\Framework\TestCase;

class HeaderUtilsTest extends TestCase
{
    public function testSplit(): void
    {
        $this->assertSame(['foo=123', 'bar'], HeaderUtils::split('foo=123,bar', ','));
        $this->assertSame(['foo=123', 'bar'], HeaderUtils::split('foo=123, bar', ','));
        $this->assertSame([['foo=123', 'bar']], HeaderUtils::split('foo=123; bar', ',;'));
        $this->assertSame([['foo=123'], ['bar']], HeaderUtils::split('foo=123, bar', ',;'));
        $this->assertSame(['foo', '123, bar'], HeaderUtils::split('foo=123, bar', '='));
        $this->assertSame(['foo', '123, bar'], HeaderUtils::split(' foo = 123, bar ', '='));
        $this->assertSame([['foo', '123'], ['bar']], HeaderUtils::split('foo=123, bar', ',='));
        $this->assertSame([[['foo', '123']], [['bar'], ['foo', '456']]], HeaderUtils::split('foo=123, bar; foo=456', ',;='));
        $this->assertSame([[['foo', 'a,b;c=d']]], HeaderUtils::split('foo="a,b;c=d"', ',;='));
        $this->assertSame(['foo', 'bar'], HeaderUtils::split('foo,,,, bar', ','));
        $this->assertSame(['foo', 'bar'], HeaderUtils::split(',foo, bar,', ','));
        $this->assertSame(['foo', 'bar'], HeaderUtils::split(' , foo, bar, ', ','));
        $this->assertSame(['foo bar'], HeaderUtils::split('foo "bar"', ','));
        $this->assertSame(['foo bar'], HeaderUtils::split('"foo" bar', ','));
        $this->assertSame(['foo bar'], HeaderUtils::split('"foo" "bar"', ','));
        // These are not a valid header values. We test that they parse anyway,
        // and that both the valid and invalid parts are returned.
        $this->assertSame([], HeaderUtils::split('', ','));
        $this->assertSame([], HeaderUtils::split(',,,', ','));
        $this->assertSame(['foo', 'bar', 'baz'], HeaderUtils::split('foo, "bar", "baz', ','));
        $this->assertSame(['foo', 'bar, baz'], HeaderUtils::split('foo, "bar, baz', ','));
        $this->assertSame(['foo', 'bar, baz\\'], HeaderUtils::split('foo, "bar, baz\\', ','));
        $this->assertSame(['foo', 'bar, baz\\'], HeaderUtils::split('foo, "bar, baz\\\\', ','));
    }

    public function testCombine(): void
    {
        $this->assertSame(['foo' => '123'], HeaderUtils::combine([['foo', '123']]));
        $this->assertSame(['foo' => true], HeaderUtils::combine([['foo']]));
        $this->assertSame(['foo' => true], HeaderUtils::combine([['Foo']]));
        $this->assertSame(['foo' => '123', 'bar' => true], HeaderUtils::combine([['foo', '123'], ['bar']]));
    }

    public function testToString(): void
    {
        $this->assertSame('foo', HeaderUtils::toString(['foo' => true], ','));
        $this->assertSame('foo; bar', HeaderUtils::toString(['foo' => true, 'bar' => true], ';'));
        $this->assertSame('foo=123', HeaderUtils::toString(['foo' => '123'], ','));
        $this->assertSame('foo="1 2 3"', HeaderUtils::toString(['foo' => '1 2 3'], ','));
        $this->assertSame('foo="1 2 3", bar', HeaderUtils::toString(['foo' => '1 2 3', 'bar' => true], ','));
    }

    public function testQuote(): void
    {
        $this->assertSame('foo', HeaderUtils::quote('foo'));
        $this->assertSame('az09!#$%&\'*.^_`|~-', HeaderUtils::quote('az09!#$%&\'*.^_`|~-'));
        $this->assertSame('"foo bar"', HeaderUtils::quote('foo bar'));
        $this->assertSame('"foo [bar]"', HeaderUtils::quote('foo [bar]'));
        $this->assertSame('"foo \"bar\""', HeaderUtils::quote('foo "bar"'));
        $this->assertSame('"foo \\\\ bar"', HeaderUtils::quote('foo \\ bar'));
    }

    public function testUnquote(): void
    {
        $this->assertEquals('foo', HeaderUtils::unquote('foo'));
        $this->assertEquals('az09!#$%&\'*.^_`|~-', HeaderUtils::unquote('az09!#$%&\'*.^_`|~-'));
        $this->assertEquals('foo bar', HeaderUtils::unquote('"foo bar"'));
        $this->assertEquals('foo [bar]', HeaderUtils::unquote('"foo [bar]"'));
        $this->assertEquals('foo "bar"', HeaderUtils::unquote('"foo \"bar\""'));
        $this->assertEquals('foo "bar"', HeaderUtils::unquote('"foo \"\b\a\r\""'));
        $this->assertEquals('foo \\ bar', HeaderUtils::unquote('"foo \\\\ bar"'));
    }
}
