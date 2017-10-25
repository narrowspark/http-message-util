<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Tests;

use Narrowspark\Http\Message\Util\InteractsWithAcceptLanguage;
use PHPUnit\Framework\TestCase;

abstract class AbstractInteractsWithAcceptLanguageTest extends TestCase
{
    private const ACCEPT_LANGUAGE = 'Accept-Language';

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    protected $request;

    public function testGetLanguagesWithEmptyLanguageHeader(): void
    {
        self::assertEquals([], InteractsWithAcceptLanguage::getLanguages($this->request));
    }

    public function testGetLanguagesWithSimpleLanguageHeader(): void
    {
        $request = $this->request->withHeader(self::ACCEPT_LANGUAGE, 'zh, en-us; q=0.8, en; q=0.6');

        self::assertEquals(['zh', 'en_US', 'en'], InteractsWithAcceptLanguage::getLanguages($request));
        self::assertEquals(['zh', 'en_US', 'en'], InteractsWithAcceptLanguage::getLanguages($request)); // cache
    }

    public function testGetLanguagesWithOutOfOrderQValues(): void
    {
        $request = $this->request->withHeader(self::ACCEPT_LANGUAGE, 'zh, en-us; q=0.6, en; q=0.8');

        self::assertEquals(['zh', 'en', 'en_US'], InteractsWithAcceptLanguage::getLanguages($request));
    }

    public function testGetLanguagesWithEqualWeightingWithoutQValues(): void
    {
        $request = $this->request->withHeader(self::ACCEPT_LANGUAGE, 'zh, en, en-us');

        self::assertEquals(['zh', 'en', 'en_US'], InteractsWithAcceptLanguage::getLanguages($request));
    }

    public function testGetLanguagesWithEqualWeightingWithQValues(): void
    {
        $request = $this->request->withHeader(self::ACCEPT_LANGUAGE, 'zh; q=0.6, en, en-us; q=0.6');

        self::assertEquals(['en', 'zh', 'en_US'], InteractsWithAcceptLanguage::getLanguages($request)); // Test equal weighting with q values
    }

    public function testGetLanguagesISO639(): void
    {
        $request = $this->request->withHeader(self::ACCEPT_LANGUAGE, 'zh, i-cherokee; q=0.6');

        self::assertEquals(['zh', 'cherokee'], InteractsWithAcceptLanguage::getLanguages($request));
    }
}
