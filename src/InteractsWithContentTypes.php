<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util;

use Narrowspark\Http\Message\Util\Traits\AcceptHeaderTrait;
use Psr\Http\Message\MessageInterface;

class InteractsWithContentTypes
{
    use AcceptHeaderTrait;

    /**
     * @var array
     */
    private static $formats = [
        'html' => 'text/html',
        'txt'  => 'text/plain',
        'js'   => 'application/javascript',
        'css'  => 'text/css',
        'json' => 'application/json',
        'xml'  => 'text/xml',
        'rdf'  => 'application/rdf+xml',
        'atom' => 'application/atom+xml',
        'rss'  => 'application/rss+xml',
        'form' => 'application/x-www-form-urlencoded',
    ];

    /**
     * Determine if the request is sending JSON.
     *
     * @param \Psr\Http\Message\MessageInterface $request
     *
     * @return bool
     */
    public static function isJson(MessageInterface $request): bool
    {
        foreach (['/json', '+json'] as $type) {
            if (\mb_strpos($request->getHeaderLine('Content-Type'), $type, 0, 'UTF-8') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the request is the result of an PJAX call.
     *
     * @param \Psr\Http\Message\MessageInterface $request
     *
     * @return bool
     */
    public static function isPjax(MessageInterface $request): bool
    {
        return $request->getHeaderLine('X-Pjax') !== '';
    }

    /**
     * Determine if the request is the result of an AJAX call.
     *
     * @param \Psr\Http\Message\MessageInterface $request
     *
     * @return bool
     */
    public static function isAjax(MessageInterface $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Return the most suitable content type from the given array based on content negotiation.
     *
     * @param array                              $contentTypes
     * @param \Psr\Http\Message\MessageInterface $request
     *
     * @return null|string
     */
    public function prefers(array $contentTypes, MessageInterface $request): ?string
    {
        $accepts = self::getHeaderValuesFromString($request->getHeaderLine('Content-Type'));

        foreach ($accepts as $accept) {
            if (in_array($accept, ['*/*', '*'], true)) {
                return $contentTypes[0];
            }

            foreach ($contentTypes as $contentType) {
                $type = $contentType;
                if (null !== $mimeType = self::getMimeType($contentType)) {
                    $type = $mimeType;
                }
                if (self::matchesType($type, $accept) || $accept === strtok($type, '/') . '/*') {
                    return $contentType;
                }
            }
        }
    }

    /**
     * Determines whether the current requests accepts a given content type.
     *
     * @param array                              $contentTypes
     * @param \Psr\Http\Message\MessageInterface $message
     *
     * @return bool
     */
    public static function accepts(array $contentTypes, MessageInterface $message): bool
    {
        $accepts = self::getHeaderValuesFromString($message->getHeaderLine('Accept'));

        if (\count($accepts) === 0) {
            return true;
        }

        foreach ($accepts as $accept) {
            if ($accept === '*/*' || $accept === '*') {
                return true;
            }

            foreach ($contentTypes as $type) {
                if (self::matchesType($accept, $type) || $accept === \strtok($type, '/') . '/*') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine if the given content types match.
     *
     * @param string $actual
     * @param string $type
     *
     * @return bool
     */
    public static function matchesType(string $actual, string $type): bool
    {
        if ($actual === $type) {
            return true;
        }

        $split = \explode('/', $actual);

        return isset($split[1]) && \preg_match('#' . \preg_quote($split[0], '#') . '/.+\+' . \preg_quote($split[1], '#') . '#', $type);
    }

    /**
     * Gets the mime type associated with the format.
     *
     * @param string $format The format
     *
     * @return null|string The associated mime type (null if not found)
     */
    private static function getMimeType($format): ?string
    {
        return static::$formats[$format] ?? null;
    }
}
