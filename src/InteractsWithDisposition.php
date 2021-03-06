<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util;

use Psr\Http\Message\ResponseInterface;

class InteractsWithDisposition
{
    /**
     * @var string
     */
    public const DISPOSITION_ATTACHMENT = 'attachment';

    /**
     * @var string
     */
    public const DISPOSITION_INLINE = 'inline';

    /**
     * Private constructor; non-instantiable.
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * A helper function for automatically encoded (ASCII) filename should be used for the fallback filename.
     *
     * @param string $filename
     *
     * @return string
     */
    public static function encodedFallbackFilename(string $filename): string
    {
        $filenameFallback = '';

        if (! \preg_match('/^[\x20-\x7e]*$/', $filename) || \mb_strpos($filename, '%') !== false) {
            $encoding = \mb_detect_encoding($filename, null, true) ?: '8bit';

            for ($i = 0, $filenameLength = \mb_strlen($filename, $encoding); $i < $filenameLength; $i++) {
                $char = \mb_substr($filename, $i, 1, $encoding);

                if ($char === '%' || \ord($char) < 32 || \ord($char) > 126) {
                    $filenameFallback .= '_';
                } else {
                    $filenameFallback .= $char;
                }
            }
        }

        return $filenameFallback === '' ? $filename : $filenameFallback;
    }

    /**
     * Generates a HTTP Content-Disposition field-value.
     *
     * @see RFC 6266
     *
     * @param string $disposition      One of "inline" or "attachment"
     * @param string $filename         A unicode string
     * @param string $filenameFallback A string containing only ASCII characters that
     *                                 is semantically equivalent to $filename. If the filename is already ASCII,
     *                                 it can be omitted, or just copied from $filename
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function makeDisposition(string $disposition, string $filename, string $filenameFallback = ''): string
    {
        if (! \in_array($disposition, [self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE], true)) {
            throw new \InvalidArgumentException(
                \sprintf('The disposition must be either "%s" or "%s".', self::DISPOSITION_ATTACHMENT, self::DISPOSITION_INLINE)
            );
        }

        if ($filenameFallback === '') {
            $filenameFallback = $filename;
        }

        // filenameFallback is not ASCII.
        if (! \preg_match('/^[\x20-\x7e]*$/', $filenameFallback)) {
            throw new \InvalidArgumentException('The filename fallback must only contain ASCII characters.');
        }

        // percent characters aren't safe in fallback.
        if (\mb_strpos($filenameFallback, '%') !== false) {
            throw new \InvalidArgumentException('The filename fallback cannot contain the "%" character.');
        }

        // path separators aren't allowed in either.
        if (\mb_strpos($filename, '/') !== false ||
            \mb_strpos($filename, '\\') !== false ||
            \mb_strpos($filenameFallback, '/') !== false ||
            \mb_strpos($filenameFallback, '\\') !== false
        ) {
            throw new \InvalidArgumentException('The filename and the fallback cannot contain the "/" and "\\" characters.');
        }

        $params = ['filename' => $filenameFallback];

        if ($filename !== $filenameFallback) {
            $params['filename*'] = "utf-8''" . \rawurlencode($filename);
        }

        return $disposition . '; ' . HeaderUtils::toString($params, ';');
    }

    /**
     * Appends the content-disposition header to response.
     *
     * @see \Narrowspark\Http\Message\Util\InteractsWithDisposition::makeDisposition()
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string                              $disposition
     * @param string                              $filename
     * @param string                              $filenameFallback
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function appendDispositionHeader(
        ResponseInterface $response,
        string $disposition,
        string $filename,
        string $filenameFallback = ''
    ): ResponseInterface {
        return $response->withHeader('Content-Disposition', self::makeDisposition($disposition, $filename, $filenameFallback));
    }
}
