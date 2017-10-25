<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util;

use Psr\Http\Message\MessageInterface;

class InteractsWithAuthorization
{
    public static function getAuthorization(MessageInterface $message): ?array
    {
        $header  = $message->getHeaderLine('Authorization');
        $matches = [];

        if (! preg_match('/^\s*(\S+)\s+(\S+)/', $header, $matches)) {
            return null;
        }

        return [$matches[1], $matches[2]];
    }
}
