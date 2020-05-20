<?php

namespace App\Lib\Markdown\Tags;

/**
 * Class TableTag
 * @package App\Lib\Markdown\Tags
 */
class TableTag extends AbstractTag
{
    /**
     * @return string
     */
    public static function getTagName(): string
    {
        return 't';
    }

    /**
     * @param string $line
     * @return array
     */
    public function parse(string $line): ?array
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isSet(): bool
    {
        return true;
    }

    /**
     * @param array $subResult
     * @return array|null
     */
    public static function convertFromText(array $subResult): ?array
    {
        $lines = reset($subResult);
        $lineNumber = 0;
        $count = null;
        $result = [];
        foreach ($lines as $line) {
            $lineResult = self::parseText($line);
            if ($lineResult === null || ($count && $count !== count($lineResult))) {
                return null;
            }
            if ($count === null) {
                $count = count($lineResult);
            }
            $lineNumber++;
            if ($lineNumber === 2) {
                if (array_unique($lineResult)[0] !== '---') {
                    return null;
                }
                continue;
            }
            $result[] = $lineResult;
        }
        return [self::getTagName() => $result];
    }

    /**
     * @param string $line
     * @return array|null
     */
    public static function parseText(string $line): ?array
    {
        if (mb_substr($line, -1) !== '|') {
            return null;
        }
        $result = [];
        foreach (explode('|', $line) as $elem) {
            $elem = trim($elem);
            if ($elem) {
                $result[] = $elem;
            }
        }
        return $result ?? null;
    }
}
