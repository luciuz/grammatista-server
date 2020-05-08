<?php

namespace App\Lib;

/**
 * Class FileParser
 * @package App\Lib
 */
class FileParser
{
    /** @var string */
    private $filename;

    /** @var callable */
    private $handler;

    /**
     * @param string   $filename
     * @param callable $handler
     */
    public function init(string $filename, callable $handler): void
    {
        $this->filename = $filename;
        $this->handler = $handler;
    }

    /**
     * Run
     */
    public function run(): void
    {
        $file = fopen($this->filename, 'rb');
        if ($file) {
            while (($line = fgets($file)) !== false) {
                call_user_func($this->handler, $line);
            }
        }
        is_resource($file) && fclose($file);
    }
}
