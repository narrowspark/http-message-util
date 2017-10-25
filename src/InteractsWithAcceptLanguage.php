<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util;

use Narrowspark\Http\Message\Util\Traits\AcceptHeaderTrait;
use Psr\Http\Message\MessageInterface;

class InteractsWithAcceptLanguage
{
    use AcceptHeaderTrait;

    /**
     * Gets a list of languages acceptable by the client browser.
     *
     * @param \Psr\Http\Message\MessageInterface $message
     *
     * @return array Languages ordered in the user browser preferences
     */
    public static function getLanguages(MessageInterface $message): array
    {
        $languagesFromString = self::getHeaderValuesFromString($message->getHeaderLine('Accept-Language'));
        $languages           = [];

        foreach ($languagesFromString as $lang) {
            if (mb_strpos($lang, '-') !== false) {
                $codes = explode('-', $lang);

                if ('i' === $codes[0]) {
                    // Language not listed in ISO 639 that are not variants
                    // of any listed language, which can be registered with the
                    // i-prefix, such as i-cherokee
                    if (count($codes) > 1) {
                        $lang = $codes[1];
                    }
                } else {
                    foreach ($codes as $i => $iValue) {
                        if (0 === $i) {
                            $lang = mb_strtolower($codes[0]);
                        } else {
                            $lang .= '_' . mb_strtoupper($codes[$i]);
                        }
                    }
                }
            }

            $languages[] = $lang;
        }

        return $languages;
    }
}
