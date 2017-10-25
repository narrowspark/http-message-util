<?php
declare(strict_types=1);
namespace Narrowspark\Http\Message\Util\Traits;

trait AcceptHeaderTrait
{
    /**
     * Gets a list of header values from header string.
     *
     * @param string $headerValue
     *
     * @return array
     */
    public static function getHeaderValuesFromString(string $headerValue): array
    {
        $index = 0;

        $items = \array_map(
            function ($itemValue) use (&$index) {
                return self::buildAcceptHeaderItem($itemValue, $index++);
            },
            \preg_split(
                '/\s*(?:,*("[^"]+"),*|,*(\'[^\']+\'),*|,+)\s*/',
                $headerValue,
                0,
                PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
            )
        );

        return self::getKeyItems($items);
    }

    /**
     * Builds an accept header array from a string.
     *
     * @param string $itemValue
     * @param int    $index
     *
     * @return array
     */
    private static function buildAcceptHeaderItem(string $itemValue, int $index): array
    {
        $bits = \preg_split(
            '/\s*(?:;*("[^"]+");*|;*(\'[^\']+\');*|;+)\s*/',
            $itemValue,
            0,
            \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE
        );
        $value             = \array_shift($bits);
        $attributes        = [
            'index' => $index,
        ];
        $lastNullAttribute = null;

        foreach ($bits as $bit) {
            if (($start = \mb_substr($bit, 0, 1)) === ($end = \mb_substr($bit, -1)) && ('"' === $start || '\'' === $start)) {
                $attributes[$lastNullAttribute] = \mb_substr($bit, 1, -1);
            } elseif ('=' === $end) {
                $lastNullAttribute = $bit = \mb_substr($bit, 0, -1);
                $attributes[$bit]  = null;
            } else {
                $parts                 = \explode('=', $bit);
                $attributes[$parts[0]] = isset($parts[1]) && \mb_strlen($parts[1]) > 0 ? $parts[1] : '';
            }
        }

        if (($start = \mb_substr($value, 0, 1)) === ($end = \mb_substr($value, -1)) &&
            ('"' === $start || '\'' === $start)
        ) {
            $value = \mb_substr($value, 1, -1);
        }

        return [$value => $attributes];
    }

    /**
     * Sorts items by descending quality.
     *
     * @param array $items
     *
     * @return array
     */
    private static function sortHeaderItems(array $items): array
    {
        uasort($items, function ($a, $b) {
            $a = array_values($a)[0];
            $b = array_values($b)[0];

            $qA = $a['q'] ?? 1.0;
            $qB = $b['q'] ?? 1.0;

            if ($qA === $qB) {
                return $a['index'] > $b['index'] ? 1 : -1;
            }

            return $qA > $qB ? -1 : 1;
        });

        return $items;
    }

    /**
     * Get only the key from the item.
     *
     * @param array $items
     *
     * @return array
     */
    private static function getKeyItems(array $items): array
    {
        $keyItems = [];

        foreach (self::sortHeaderItems($items) as $item) {
            $keyItems[] = key($item);
        }

        return $keyItems;
    }
}
