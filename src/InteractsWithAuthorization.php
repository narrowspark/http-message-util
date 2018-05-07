<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util;

use Psr\Http\Message\MessageInterface;

class InteractsWithAuthorization
{
    /**
     * Private constructor; non-instantiable.
     *
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Get the authorization from http header.
     *
     * @param \Psr\Http\Message\MessageInterface $message
     *
     * @return null|array
     */
    public static function getAuthorization(MessageInterface $message): ?array
    {
        $header  = $message->getHeaderLine('Authorization');
        $matches = [];

        if (! \preg_match('/^\s*(\S+)\s+(\S+)/', $header, $matches)) {
            return null;
        }

        return [$matches[1], $matches[2]];
    }
}
